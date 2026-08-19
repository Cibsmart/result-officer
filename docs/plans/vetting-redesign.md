# Vetting Redesign

Design for the replacement vetting subsystem. The existing implementation
(`App\Services\Vetting\Vetting`, `App\Actions\Vetting\*`, `VettingEvent` / `VettingStep` /
`VettingReport` / `VettingEventGroup`) is **not** being improved — it is being rebuilt and then
deleted. See §13 for the cutover.

## 1. Decisions

All nine decisions below are settled. Everything else in this document follows from them.

| # | Decision | Consequence |
|---|----------|-------------|
| 1 | **Rules are data.** The rule set is configurable per program / curriculum, not hardcoded. | §2 splits *checks* (code) from *rules* (data). Needs a rule-set editor and versioning. |
| 2 | **Vetting is pure verification.** It reads records and reports; it never writes to them. | The three mutating behaviours move out to an explicit reconciliation action (§9). Runs become repeatable and safe. |
| 3 | **Runs are immutable.** Each run is a new, frozen record. | Full audit trail: who vetted whom, when, against which rule-set version, and what changed between runs. |
| 4 | **Waivers exist.** A privileged user can waive a specific finding, with a reason and an audit record. | Findings need stable, addressable identity across runs (§7). |
| 5 | **Full assignment chain.** ProgramCurriculum → Program → Department → Faculty → global. | Forces the lineage/version split in §3.1 and a resolution-preview tool in §11. The largest single source of complexity in this design. |
| 6 | **Staleness blocks clearance.** A passing run whose underlying data has changed no longer authorises clearance. | Needs a precisely scoped digest (§6.3) or it fires constantly on noise. |
| 7 | **Waivers: single role, approval fields reserved.** | Unused approval columns and a config flag, so two-person approval switches on later with no migration (§7). |
| 8 | **Archived students are re-vettable as audit-only runs.** | An `is_audit` flag that also unlocks draft rule-set dry runs (§8.3). |
| 9 | **Advisory by default:** `semester_credit_load`, `first_year_courses_in_first_year`, `study_year_sequence`. All other checks blocking. | Seed data only — editable in the UI at any time (§5). |

### Assumptions

- Execution moves to **queued jobs on Horizon**. Horizon, Redis and Reverb are already installed and
  currently carry no application work; the existing `everyMinute` / `defer()` approach has no
  retries, no backoff and no failure visibility.
- **Filament 3** hosts rule-set administration. Rule sets are back-office configuration edited by a
  small number of privileged users. Student-facing and registrar-facing screens stay in Inertia/Vue.
- The new subsystem runs **alongside** the old one until parity is demonstrated (§13). No table,
  class or route name below collides with an existing one.
- Assignment resolution is governed by the student's **entry session**, matching catalogue-year
  semantics and consistent with `ProgramCurriculum` itself being keyed by `entry_session_id`. See
  §14.1 if the session of vetting would be more correct.

---

## 2. Vocabulary

The single most important distinction in this design:

> A **check** is *code*: a named, parameterised evaluator, e.g. "required courses were taken".
> A **rule** is *data*: a check bound to concrete parameters and a severity.
> A **rule set** is a named lineage; a **rule-set version** is a frozen, publishable list of rules.
> A **run** is one evaluation of one rule-set version against one student, frozen forever.
> A **finding** is one specific problem a rule detected, addressable by fingerprint.
> A **waiver** forgives one finding for one student, with a reason and an author.

Keeping "check" and "rule" distinct is what makes rules-as-data tractable: the catalogue of checks is
a closed set maintained by developers, while the rules built from them are open configuration. There
is no expression language and no user-authored logic — a deliberate limit.

---

## 3. Domain model

### 3.1 Configuration side

Decision 5 forces a **lineage / version split**. If assignments pointed directly at a version, then
publishing v2 of a rule set would require re-pointing every assignment across five levels — and
missing one would silently leave a cohort on the old rules. So assignments point at the *lineage*,
resolution selects its currently `active` version, and the run records the concrete version it
evaluated.

```
vetting_rule_sets                 -- the lineage
  id, name, slug (unique), description, timestamps

vetting_rule_set_versions         -- the frozen, publishable thing
  id, vetting_rule_set_id, version, status(draft|active|archived),
  notes, created_by, published_at, timestamps
  unique(vetting_rule_set_id, version)

vetting_rules
  id, vetting_rule_set_version_id, check_key, position,
  severity(blocking|advisory), enabled, config(json), timestamps
  unique(vetting_rule_set_version_id, check_key)

vetting_rule_set_assignments
  id, vetting_rule_set_id,
  assignable_type, assignable_id,        -- both null = global default
  effective_from_session_id (nullable), timestamps
  unique(assignable_type, assignable_id, effective_from_session_id)
```

**Invariants.**

- Exactly **one `active` version per lineage**, enforced inside the publish transaction. Publishing
  archives its predecessor.
- Exactly **one global assignment exists at all times** — seeded and undeletable. This guarantees
  resolution always terminates, so there is no "no rule set found" failure mode to design around.
- A version is immutable once `active`. Editing clones it to a new `draft`.
- The unique index on assignments is what prevents two equally-specific assignments from making
  resolution ambiguous.

**Resolution.** Most specific first; the first level with a match wins.

```
1. ProgramCurriculum   the student's resolved curriculum
2. Program             students.program_id
3. Department          program.department_id
4. Faculty             department.faculty_id
5. Global              assignable_type/id null
```

Within a level, candidates are those whose `effective_from_session_id` is null or is at or before the
student's entry session; the latest such assignment wins, with null ordered last. The resolver
returns both the version and the level it matched at, and the run records the latter (`resolved_via`)
— with five levels in play, a historical run must be able to explain why it used the rule set it used.

### 3.2 Result side

```
vetting_runs
  id, student_id, vetting_rule_set_version_id, program_curriculum_id (nullable),
  vetting_batch_id (nullable), triggered_by_user_id,
  status, resolved_via, is_audit,
  record_digest, curriculum_digest, failure_reason (nullable),
  counts: passed_count, failed_count, inconclusive_count, waived_count, advisory_count,
  started_at, completed_at, timestamps
  index(student_id, completed_at)

vetting_rule_results
  id, vetting_run_id, check_key, position, severity, status,
  summary, config_snapshot(json), timestamps
  unique(vetting_run_id, check_key)

vetting_findings
  id, vetting_rule_result_id, fingerprint, severity,
  subject_type, subject_id (both nullable — not every finding has a model),
  message, context(json), vetting_waiver_id (nullable), timestamps
  index(fingerprint)

vetting_waivers
  id, student_id, check_key, fingerprint, reason, authority_reference (nullable),
  granted_by_user_id, granted_at, expires_at (nullable),
  requested_by_user_id (nullable),     -- reserved, see §7
  approved_by_user_id (nullable),      -- reserved
  approval_state (default not_required),-- reserved
  revoked_by_user_id (nullable), revoked_at (nullable),
  finding_snapshot(json), timestamps
  index(student_id, fingerprint)

vetting_batches
  id, user_id, department_id, title, slug, status,
  job_batch_id (nullable — Laravel's batch id), student_count,
  queued_at, completed_at, timestamps
```

`students.latest_vetting_run_id` — nullable FK, updated in the same transaction that completes a
non-audit run. A pointer column rather than an `is_latest` flag: partial unique indexes are not
portable to MySQL, and `MAX(completed_at)` on every read is wasteful when the clearance gate consults
it constantly.

A run **belongs to at most one batch**, replacing the old many-to-many between `VettingEventGroup`
and `VettingEvent`. Runs are per-execution rather than per-student, so the many-to-many has nothing
left to express.

`authority_reference` (senate minute number, memo reference) is an addition, not something you chose
— it makes a single-role waiver defensible by recording the external authority the waiver documents.
Drop it if waivers are genuinely originated in-system.

### 3.3 Class names

| Concern | Class |
|---------|-------|
| Check contract | `App\Contracts\VettingCheck` |
| Check catalogue | `App\Enums\VettingCheckKey` |
| Check implementations | `App\Vetting\Checks\*` |
| Per-check config DTOs | `App\Vetting\Config\*Config` (spatie/laravel-data) |
| Engine | `App\Vetting\Engine\VettingEngine` |
| Rule-set resolution | `App\Vetting\Engine\RuleSetResolver`, `Resolution` |
| Snapshot + loader | `App\Vetting\Engine\StudentRecord`, `StudentRecordLoader` |
| Models | `VettingRuleSet`, `VettingRuleSetVersion`, `VettingRule`, `VettingRuleSetAssignment`, `VettingRun`, `VettingRuleResult`, `VettingFinding`, `VettingWaiver`, `VettingBatch` |
| Auth policies | `VettingRuleSetPolicy`, `VettingRunPolicy`, `VettingBatchPolicy`, `VettingWaiverPolicy` |

`app/Vetting/` is a deliberate deviation from the layered-folder convention in `CLAUDE.md`. The
engine, resolver, snapshot and check catalogue are one cohesive subsystem with an internal registry,
and scattering them across `Services/`, `Queries/` and `Actions/` is what made the current
implementation hard to follow. Models, Data DTOs, Actions and Controllers stay in their conventional
homes. Update `CLAUDE.md` when this lands.

---

## 4. The engine

### 4.1 Snapshot first, then pure checks

```php
$record     = StudentRecordLoader::for($student)->load();     // ~6 queries, once
$resolution = RuleSetResolver::for($student)->resolve();      // version + level
$run        = VettingEngine::run($record, $resolution, $triggeredBy);
```

`StudentRecord` is an immutable snapshot assembled by a single loader:

```php
final readonly class StudentRecord
{
    public function __construct(
        public Student $student,
        public ?ProgramCurriculum $curriculum,
        public CurriculumBlueprint $blueprint,   // levels → semesters → courses, elective groups
        public EnrollmentTimeline $timeline,     // session + semester enrollments, ordered
        public RegistrationSet $registrations,   // registrations + results, pre-indexed
        public string $recordDigest,
        public string $curriculumDigest,
    ) {}
}
```

Every collection inside is **pre-indexed** for the access patterns the checks need — registrations by
course id, by curriculum course id, and by semester enrollment; curriculum courses by semester and by
type. Checks never issue a query.

This one decision resolves four separate defects in the current implementation at once:

- the N+1 loops in `VerifyCoursesCreditUnit` and `VerifyFailedCourses`;
- the five uncached repeat calls to `Student::programCurriculum()` per run;
- the per-student mutable state on `ReportVettingStep`, shared across every student in a batch;
- untestability — checks become pure functions, testable against a hand-built snapshot with no
  database and no factories.

### 4.2 The check contract

```php
interface VettingCheck
{
    public static function key(): VettingCheckKey;

    /** @return class-string<\Spatie\LaravelData\Data> */
    public static function configClass(): string;

    /** @return array<int, \App\Enums\VettingCheckKey> */
    public static function prerequisites(): array;

    public function evaluate(StudentRecord $record, Data $config): CheckOutcome;
}
```

```php
final readonly class CheckOutcome
{
    /** @param array<int, Finding> $findings */
    public function __construct(
        public CheckStatus $status,
        public array $findings = [],
        public string $summary = '',
    ) {}
}
```

Checks are stateless and registered in a `CheckRegistry` keyed by `VettingCheckKey`, so a rule row's
`check_key` resolves to an implementation and its `config` JSON hydrates into that check's typed
config DTO. Configuration is validated against the DTO when a version is saved — not at run time.

### 4.3 Prerequisites

`prerequisites()` declares what a check depends on. The engine sorts by them and, when a prerequisite
did not pass, short-circuits the dependent to `INCONCLUSIVE` with `prerequisite <key> did not pass`.

This replaces the current arrangement, where ordering is encoded only in the position of entries in
an array literal in `AppServiceProvider`, under a `phpcs:ignore` that suppresses the alphabetical-sort
rule. It also fixes the reporting noise: today a single missing curriculum produces separate failure
lists from five different steps; here it produces one root-cause finding and four explicit
`INCONCLUSIVE` results.

---

## 5. Check catalogue

`Sev.` is the seeded default from decision 9, editable per rule-set version.

### Ported from the current implementation

| Key | Sev. | Checks that | Config |
|-----|------|-------------|--------|
| `curriculum_resolved` | blocking | The student maps to exactly one `ProgramCurriculum`. Prerequisite of everything curriculum-dependent. | — |
| `registrations_matched` | blocking | Every registration maps to a curriculum course. | `allow_unmatched`, `ignore_course_types` |
| `enrollments_matched` | blocking | Every semester enrollment maps to a curriculum semester. | — |
| `result_integrity` | blocking | `Result::getData()` matches the stored `ResultDetail::value`. | — |
| `required_courses_taken` | blocking | All mandatory curriculum courses were attempted. | `course_types` (default `[C, R, G]`), `require_pass` |
| `elective_requirements_met` | blocking | Per-semester elective count, units and group completeness. | `enforce_count`, `enforce_units`, `enforce_groups` |
| `credit_unit_matches_curriculum` | blocking | Registration credit unit equals the curriculum's. | `tolerance` |
| `outstanding_failures` | blocking | No failed course remains unpassed. | `pass_grades` (default `Grade::passGrade()`), `allow_carryover` |
| `semester_credit_load` | **advisory** | Semester totals sit within limits. Advisory because carryover and light final semesters make it the highest-false-positive check in the set. | `use_curriculum_limits`, `min`, `max` (fallback: `CreditUnit::minSemesterUnit()` / `maxSemesterUnit()`) |
| `first_year_courses_in_first_year` | **advisory** | First-year curriculum courses were taken in year one. Advisory because a course never taken at all is already caught, blocking, by `required_courses_taken`. | `course_types` |
| `study_year_sequence` | **advisory** | Session enrollments form a strictly increasing year sequence. Advisory because it is almost always a data-ordering artefact, now fixable through `NormalizeStudyYears` (§9) rather than silently during vetting. | — |

### New — cheap to add once rules are data

| Key | Sev. | Checks that | Config |
|-----|------|-------------|--------|
| `minimum_total_credit_units` | blocking | Total passed credit units meet the graduation floor. | `minimum` |
| `minimum_cgpa` | blocking | Final CGPA meets the threshold. | `minimum` |
| `maximum_duration` | blocking | Time to completion is within the allowed span. | `max_sessions` |
| `student_status_eligible` | blocking | Status is in `StudentStatus::vettableStates()`. | `allowed_statuses` |

The last four are the payoff for decision 1: adding a graduation requirement becomes a rule-set edit
plus one small class, instead of a new enum case, a new action, a new wrapper service and a container
binding.

---

## 6. Status semantics

### 6.1 Six check statuses, not one bucket

| Status | Meaning | Blocks a pass? |
|--------|---------|----------------|
| `PASSED` | Evaluated, satisfied. | No |
| `FAILED` | Evaluated, not satisfied. | Yes, if severity is blocking |
| `NOT_APPLICABLE` | Legitimately does not apply — e.g. a curriculum with no elective requirements. | No |
| `INCONCLUSIVE` | Could not be determined — missing curriculum, unmet prerequisite, absent data. | **Yes** |
| `WAIVED` | Failed, but every blocking finding is covered by a live waiver. | No |
| `SKIPPED` | The rule is disabled in this version. | No |

**This is the single most important correction in the redesign.** The current
`VettingEvent::updateVettingStatus()` asks only "does any step have status `FAILED`?" and routes
everything else — including `UNCHECKED` — into `PASSED`. A student with no matching curriculum
produces five `UNCHECKED` steps, is marked `PASSED`, satisfies `Student::canBeCleared()`, and is
cleared for graduation having never been checked against a curriculum at all. Splitting "does not
apply" from "could not be determined" is what closes that hole.

### 6.2 Run status

```
ERRORED      an unhandled throwable; failure_reason recorded
CANCELLED    batch cancelled before this run started
FAILED       ≥1 blocking rule FAILED
INCOMPLETE   no blocking failure, but ≥1 rule INCONCLUSIVE
PASSED       every enabled rule is PASSED, WAIVED or NOT_APPLICABLE
```

Only `PASSED` authorises clearance. `INCOMPLETE` is the state the old design could not express, and
is exactly the state most stuck students are actually in.

### 6.3 Staleness and the clearance gate

```php
public function canBeCleared(): bool
{
    $run = $this->latestVettingRun;

    return $run !== null
        && $run->status === VettingRunStatus::PASSED
        && $run->isCurrentFor($this)
        && StudentStatus::canBeCleared($this->status);
}
```

Staleness has **three possible triggers**, and they are deliberately not treated alike:

| Trigger | Effect | Why |
|---------|--------|-----|
| Student academic records changed | **Blocks** (`record_digest`) | The verdict is about data that no longer exists. |
| Curriculum changed | **Blocks** (`curriculum_digest`) | The requirements the student was measured against have moved. |
| Rule-set version republished | **Advisory** | The run remains a truthful verdict under the rules then in force. Blocking here would invalidate every outstanding pass the moment a version is published — operationally painful and rarely what is meant. |

The digest must be **scoped to academically material fields only**, or decision 6 turns into constant
false staleness:

```
record_digest     = sha256 of, canonically ordered:
                      registrations (id, course_id, credit_unit, course_status, semester_enrollment_id)
                      results       (registration_id, total_score, grade, grade_point)
                      enrollments   (session_id, level_id, year, semester_id)

curriculum_digest = sha256 of program_curriculum_id
                      + curriculum courses (id, course_id, course_type, credit_unit)
                      + semester minimums (min/max credit units, elective count, elective units)
```

Timestamps, `updated_at` and non-academic columns are excluded. Deleted registrations change the
digest naturally, by leaving the list. Storing the two digests separately lets the UI say *which*
changed, rather than only that something did.

---

## 7. Waivers

A waiver forgives **one finding**, not a rule and not a student. That requires findings to be
identifiable across runs, which is what `fingerprint` provides:

```
fingerprint = sha256(check_key | subject_type | subject_id | discriminator)
```

The discriminator distinguishes multiple findings from one check against one subject (e.g. the
elective check reporting both a count and a unit deficiency for the same semester). Fingerprints are
stable across runs as long as the underlying problem is the same problem — and change when it is
not, which is the property that stops a waiver silently forgiving something else later.

**Applying waivers.** At the end of a run, each blocking finding is matched against live waivers for
that student. Matched findings are linked to their waiver and stop blocking; a rule whose every
blocking finding is waived becomes `WAIVED`. Advisory findings are never waived — they do not block.

**Reserved approval (decision 7).** Ship single-role granting, but write the liveness predicate now
as though approval existed:

```php
$live = $waiver->revoked_at === null
    && ($waiver->expires_at === null || $waiver->expires_at->isFuture())
    && in_array($waiver->approval_state, [ApprovalState::NOT_REQUIRED, ApprovalState::APPROVED], true);
```

With `config('vetting.waivers.require_approval')` false, granting writes `NOT_REQUIRED` and the
waiver is live immediately. Flipping the flag later makes new waivers start `PENDING`; existing ones
keep working, the matcher is unchanged, and no migration is needed. That predicate is the whole
payoff of reserving the columns — if it is written as `revoked_at === null` today, the reservation
buys nothing.

**Audit.** `finding_snapshot` freezes the finding's message and context at grant time, so the record
shows what was actually forgiven even if the message wording later changes. Grant and revoke are
recorded through the already-installed spatie/activitylog. Waivers are never hard-deleted, only
revoked.

---

## 8. Execution

### 8.1 Single student

```
VettingController::store
  → authorize
  → VettingRun created (PENDING)
  → VetStudentJob::dispatch($run)
  → 202 + redirect
```

`VetStudentJob` has `$tries = 3`, exponential `backoff()`, and a `failed()` handler that marks the run
`ERRORED` with the throwable's message. This replaces
`defer(fn () => Artisan::call('rp:vet', ...))`, which runs the full battery inside a php-fpm child
after the response flushes, with no retry and no surfaced failure.

### 8.2 Batch

```
VettingBatchController::store
  → validate + authorize
  → VettingBatch created, one PENDING VettingRun per student
  → Bus::batch($jobs)->allowFailures()->name(...)->dispatch()
  → batch id stored on the VettingBatch
```

Laravel's job batching supplies what the current single-tick command cannot:

- **per-student isolation** — `allowFailures()` means one student's exception marks that run `ERRORED`
  and the batch continues, instead of stranding the group and every student after it;
- **progress** — `processedJobs / totalJobs` drives the progress bar directly;
- **cancellation** — a real cancel, not "only `QUEUED` groups may be deleted";
- **completion hooks** — `then` / `catch` / `finally` close the batch, so no group can sit in
  `RUNNING` forever.

`VettingStatusUpdated` broadcasts on run completion, as today, plus a batch-level event for the index
page.

### 8.3 Audit runs

Decision 8 gives runs an `is_audit` flag. An audit run never updates
`students.latest_vetting_run_id` and never affects clearance. It is forced on when the student's
status is in `StudentStatus::archivedStates()`, and can be requested explicitly for anyone else.

Combined with decision 5, this unlocks the feature that makes a five-level assignment chain safe to
operate: **impact preview**. A draft rule-set version can be dry-run over a cohort — a department, a
graduating class, or a set of already-cleared students — before it is published. Publishing a version
that silently starts failing a hundred students is otherwise very easy to do and very hard to notice.
Audit runs are the only runs permitted against a `draft` version.

The retrospective use is the other half: re-running today's rules against students cleared years ago
answers "was this degree correctly awarded?" without touching their record.

### 8.4 Commands

`rp:vet-student {student} {--rule-set=} {--audit}` and `rp:vet-batch {batch}` remain for operators,
but dispatch jobs rather than working inline. `rp:vetting:sweep` fails runs stuck in `RUNNING` past a
threshold — the general answer to "a transition failed halfway" that the codebase currently lacks in
several subsystems.

---

## 9. Reconciliation is not vetting

Three current checks write to academic records under names that read as read-only:

| Current step | Writes | Moves to |
|--------------|--------|----------|
| `MatchCurriculumCourses` | `registrations.program_curriculum_course_id` | `ReconcileRegistrationCurriculumCourses` |
| `MatchCurriculumSemesters` | `semester_enrollments.program_curriculum_semester_id` | `ReconcileEnrollmentCurriculumSemesters` |
| `OrganizeStudyYear` | `session_enrollments.year` | `NormalizeStudyYears` |

These become explicit Actions under `App\Actions\Curriculum\`, composed by
`ReconcileStudentRecord::execute(Student $student)`, each wrapped in `DB::transaction()` and each
returning a summary of what it changed. They are invoked deliberately — from a UI action, from a
command, or as a post-import step — never as a side effect of asking a question.

Vetting then *checks* the same conditions (`registrations_matched`, `enrollments_matched`,
`study_year_sequence`) and reports what is unmatched rather than quietly fixing it.

**Cutover consequence, worth planning for:** matching currently happens implicitly during vetting. A
pure verification pass over students who have never been reconciled will report large numbers of
unmatched findings. Phase 0 in §13 backfills reconciliation before any parity comparison, or the
comparison is meaningless.

---

## 10. Authorization

The current `VettingEventController::show` and `destroy` accept a route-bound group with no ownership
or permission check, and no vetting policy exists in `app/Policies`. The new subsystem ships four
policies from the start:

| Policy | Rules |
|--------|-------|
| `VettingRunPolicy` | `view` — user's department or an explicit vetting permission. `create` — vetting permission. `createAudit` — same, plus draft-version access. |
| `VettingBatchPolicy` | `view`/`delete`/`cancel` — owner or department head. |
| `VettingWaiverPolicy` | `create`/`revoke` — a dedicated privileged role, deliberately narrower than vetting itself. `approve` — reserved (§7). |
| `VettingRuleSetPolicy` | `viewAny`/`create`/`update`/`assign`/`publish` — admin only. Publishing changes graduation criteria; with a five-level chain, `assign` is nearly as consequential and is separated for that reason. |

---

## 11. Surface

**Inertia pages** (`resources/js/pages/vetting/`)

- `index` — batches, live progress, filters by status/department.
- `batch/show` — runs in a batch, grouped by curriculum, per-student status.
- `run/show` — one run: rule results in order, findings under each, waiver affordance on blocking
  findings, prominent banners for `INCOMPLETE`, stale, and audit-only.

**Filament** — `VettingRuleSetResource`, carrying most of the weight of decision 5:

- versions with draft/publish/archive, and per-rule severity, enable and config, with forms generated
  from each check's config DTO;
- the **assignment matrix** across all five levels;
- a **resolution preview** — enter a registration number, see which version resolves and the full
  chain walk that produced it. With five levels this is not a convenience; without it, nobody can
  answer "why did this student get these rules?";
- an **impact preview** — dry-run a draft version over a cohort as audit runs (§8.3) and diff against
  the current active version, before publishing.

**API** — `GET /api/student/{student}/vetting/latest`, returning the latest non-audit run with results
and findings. Unlike the current endpoint, it returns a null-safe empty payload for a never-vetted
student rather than a 500.

---

## 12. Testing

- **Checks** — pure functions over an in-memory `StudentRecord`. No database, no factories, fast.
  One test class per check covering pass / fail / not-applicable / inconclusive.
- **Roll-up matrix** — table-driven test over combinations of rule statuses asserting run status.
  This is the rule the current system gets wrong; it should be the most thoroughly tested thing here.
- **Resolver** — one test per level, plus session narrowing, null-ordering, the global fallback, and
  the ambiguity guard. Five levels means the resolver is the second most likely source of subtle
  wrongness after the roll-up.
- **Engine** — prerequisite short-circuiting, disabled rules, config hydration, waiver application.
- **Staleness** — a passing run, then a mutation of each digest input, asserting it goes stale; and
  the negative case, that a touch of `updated_at` alone does not.
- **Loader** — a query-count assertion, so the snapshot cannot silently regress into N+1s. Consider
  enabling `Model::shouldBeStrict()` in the testing environment; it is currently local-only, so lazy
  loading is not caught in CI.
- **Audit runs** — assert they never touch `latest_vetting_run_id` or clearance, including for
  archived students.
- **Jobs and batches** — `Bus::fake()` for dispatch, plus a real batch test for failure isolation.
- **Authorization** — one test per policy method, including the negative case.
- **Parity harness** (§13) — a command that runs old and new against the same students and diffs.

---

## 13. Build and cutover

| Phase | Work | Exit criterion |
|-------|------|----------------|
| **0. Reconcile** | Extract the three mutating behaviours (§9). Backfill across all vettable students. | Reconciliation runs standalone; old vetting still passes its tests with the mutation removed from its path. |
| **1. Foundation** | Schema, models, enums, `StudentRecord` + loader, engine, registry, resolver, waiver model. Seed the global default assignment. No checks, no UI. | Engine runs an empty rule set end to end; resolver returns the global default for every student. |
| **2. Catalogue** | The eleven ported checks (§5), each with tests. Seed a default version matching today's behaviour, with the three advisory severities from decision 9. | Every check tested in all four statuses. |
| **3. Parity** | `rp:vetting:compare` runs old and new over a department and diffs verdicts. Use audit runs over already-cleared students as a second corpus. | Differences are all explained — each is either a new-system fix or a new-system bug. |
| **4. Execution** | Jobs, batches, broadcasting, sweeper, commands, staleness digests. | A 400-student batch completes with per-student failure isolation. |
| **5. Surface** | Inertia pages, Filament resource with assignment matrix + resolution preview + impact preview, API, policies. | Registrar can run, read, waive and assign without touching the old UI. |
| **6. Switch** | Point `Student::canBeCleared()` at `latestVettingRun`. Add the four new checks (§5). | Clearance authorised solely by new runs. |
| **7. Remove** | Delete old classes, routes, Vue pages, tests, container bindings. Archive `vetting_events` / `vetting_steps` / `vetting_reports` / `vetting_event_groups`, then drop. Update `CLAUDE.md`. | `grep -r VettingEvent app/` is empty. |

Phase 3 is what earns the rewrite's safety. It is also the phase most likely to be skipped under time
pressure — the parity command is small and worth building properly.

**On dropping the old tables:** export them first. They are the only record of who was vetted and
cleared under the old system, and clearance decisions traceable to them may need defending years
later.

---

## 14. Remaining open items

Smaller than the nine settled decisions, and none of them block Phase 1.

1. **Governing session for resolution.** Assumed to be the student's **entry session**
   (catalogue-year semantics). The alternative — the session in which vetting occurs — would mean a
   student's applicable rules change as they progress. Worth confirming with the registry before
   Phase 5, since it is visible in the assignment UI.
2. **Who administers assignments.** `VettingRuleSetPolicy::assign` is separated from `publish` in
   §10, but both currently mean "admin". If assignment should sit with faculty officers rather than
   system administrators, that is a role, not a code change.
3. **Publish gating.** Should publishing a version require an impact preview (§8.3) to have been run
   first? A governance nicety that is nearly free while the preview is being built, and awkward to
   bolt on afterwards.
4. **Advisory findings on cleared students.** An advisory finding does not block clearance, but it is
   still a recorded irregularity on a graduate's file. Decide whether these surface anywhere after
   clearance — an internal report, or nowhere.
