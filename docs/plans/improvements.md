# Result Officer — Critical Review & Improvement Backlog

**Date:** 2026-08-11 · **Branch reviewed:** `task/upgrade` (at `ac6d66d9`)
**Status:** Review only — nothing in this document has been implemented.

---

## 0. Scope and method

Read-only review of the Laravel/Inertia/Vue codebase: 493 PHP files under `app/`, 74 migrations,
292 Vue SFCs, 184 test files, plus routing, CI, config and tooling. Findings below are grouped by
severity. Every item cites the file (and line where useful) that motivated it. Items marked
**[verified]** were confirmed by reading the code path, not inferred from naming.

### What is already good

Worth stating plainly, because it shapes the recommendations — this is a well-structured codebase
and most items below are localized fixes rather than rewrites.

- **Consistent layering.** Actions / Data / ViewModels / Queries / Pipelines / Policies are real
  boundaries, not folders-in-name-only. Controllers are genuinely thin.
- **No god classes.** Largest PHP file is `app/Models/Student.php` at 342 lines; largest Vue SFC is
  269 lines. That is unusually disciplined for a domain this size.
- **Strict typing throughout.** `declare(strict_types=1)` everywhere, `final` by default,
  `arch()->preset()->strict()` enforced in `tests/ArchTest.php`, PHPStan at level 9, TS `strict: true`.
- **No `env()` outside `config/`** — config caching is safe. Verified by grep.
- **No SQL injection surface.** All `selectRaw`/`whereRaw` usages are static strings with no
  interpolated input (`app/Models/ImportEvent.php:141-145`, two one-time commands).
- **Frontend hygiene.** Zero `console.log` left in `resources/js`; only 14 `any`/`as any` across
  343 TS+Vue files.
- **Morph map is enforced** (`AppServiceProvider::boot`), so polymorphic rows are not tied to FQCNs.

---

## 1. P0 — Confirmed correctness bugs

These are defects with a demonstrable wrong outcome. All are small, isolated fixes.

### 1.1 Four migrations drop the wrong table on rollback **[verified]**

A scripted comparison of `Schema::create(...)` against `dropIfExists(...)` in each migration found
four mismatches, including a mutually-swapped pair:

| Migration | Creates | Drops on rollback |
|---|---|---|
| `2024_07_03_184451_create_registrations_table.php` | `registrations` | **`results`** |
| `2024_07_09_091519_create_results_table.php` | `results` | **`registrations`** |
| `2024_08_19_095656_create_pulse_tables.php` | `pulse_aggregates` | `pulse_values` |
| `2024_09_13_091811_create_import_events_table.php` | `import_events` | `result_import_events` |

The first two are the dangerous pair: `migrate:rollback` over that range drops `results` while
attempting to reverse `registrations`, and vice versa — the two most valuable tables in the system.
`results` is never dropped by its own `down()`, so a rollback-and-remigrate cycle fails on a table
that still exists.

**Fix:** correct each `down()` to drop the table its `up()` created. Consider an arch/unit test that
asserts create/drop symmetry across `database/migrations` so this cannot regress.

### 1.2 `Role::creatable()` swaps the desk-officer and exam-officer labels **[verified]**

`app/Enums/Role.php:24-25`:

```php
self::DESK_OFFICER->value => 'EXAM OFFICER',   // wrong
self::EXAM_OFFICER->value => 'DESK OFFICER',   // wrong
```

`getLabel()` at lines 35-36 maps the same two cases correctly, so the enum contradicts itself. This
array feeds the role dropdown in `app/Filament/Resources/UserResource.php:35` — an admin creating a
user and picking "EXAM OFFICER" persists `desk-officer`, and the user list then renders that same
user as "DESK OFFICER". Every account created through this form since the bug landed has a role one
step away from what the operator intended.

**Fix:** swap the two values. Then audit existing `users.role` values, because assignments made via
this form are suspect. Deriving the array from `getLabel()` would remove the duplication that caused
the divergence.

### 1.3 `ClearanceController` never rolls back its transaction **[verified]**

`app/Http/Controllers/ClearanceController.php:42-50`: `DB::beginTransaction()` at 42, `DB::commit()`
at 45, and two `catch` blocks at 46 and 48 that return a redirect — **neither calls
`DB::rollBack()`**. The request returns with an open transaction.

Under `php-fpm` the connection teardown usually discards it, so this hides in development. Under a
persistent worker (Octane, or if this path is ever reached from a queue context) the open
transaction leaks into the next unit of work, which can commit a partial `FinalStudent` record
belonging to a failed clearance. Clearance is the step that promotes a student to graduand — partial
writes here corrupt the graduation record.

Two secondary problems in the same block:

- `catch (Exception)` followed by `catch (Throwable)` is redundant; `Exception` implements
  `Throwable`, so the second block only catches `Error`. One `catch (Throwable)` is equivalent.
- `$e->getMessage()` is rendered straight to the user, leaking internal detail (SQL fragments,
  table names) into the UI.

**Fix:** use the closure form `DB::transaction(fn () => ...)`, which rolls back automatically. Log
the exception and show the user a generic message.

### 1.4 Excel import events get stranded in `PROCESSING` on validation failure **[verified]**

`app/Console/Commands/ProcessRawExcelUploads.php`: line 32 sets the event to `PROCESSING`. When
preprocess checks collect errors, lines 39-41 call `setMessage(...)` and return `FAILURE` — **without
setting the status to `FAILED`**. The catch block at 50-51 does it correctly, so the omission is
clearly unintentional.

The consequence is permanent: the command's own query (lines 25-27) only selects `UPLOADED` and
`REPROCESS` events, so an event left in `PROCESSING` is never retried, and because it is not `FAILED`
the UI does not present it as failed either. It simply disappears — the user sees an upload stuck
mid-progress-bar forever, with the error message attached to a record nothing surfaces.

A related gap: `catch (Exception $e)` at line 49 does not catch `Error`. A `TypeError` from malformed
spreadsheet data — precisely the input this command is built to handle — strands the event the same
way.

**Fix:** set `ImportEventStatus::FAILED` on the validation-failure path; widen the catch to
`Throwable`. Consider a reconciliation command that fails events stuck in `PROCESSING` past a
threshold.

### 1.5 `rp:process-queued-vetting` is never scheduled — group vetting cannot run **[verified]**

`CLAUDE.md` states group vetting is "processed one group at a time by the scheduled
`rp:process-queued-vetting` command". A grep across `app/`, `routes/` and `config/` finds the string
only in the command's own class and signature. `routes/console.php` schedules
`ProcessQueuedImportEvent`, `UploadPendingExcelImports`, `ProcessRawExcelUploads` and the two backup
commands — **not** `ProcessQueuedVettingCommand`.

`VettingEventController::store` accepts a batch and sets it `QUEUED`. Nothing ever transitions it.
Every batch vetting request submitted in production is sitting in `QUEUED` and always will be, unless
an operator runs the command by hand. Single-student vetting still works because
`VettingController::create` calls it directly, which is likely why this went unnoticed.

**Fix:** add the schedule entry with `->withoutOverlapping()` (see §3.1 — a queued job is the better
target). Add a feature test asserting the command is registered in the schedule.

### 1.6 `ResultUpdateAction` audits stale values **[verified]**

`app/Actions/Results/ResultUpdateAction.php:32` calls `$registration->fresh();` and **discards the
return value**. `fresh()` returns a new instance; it does not mutate the receiver — `refresh()` does.

The next statement builds the audit record's `new` value from `$registration->getUpdateData()`, which
reads `$this->result` (`app/Models/Registration.php:210`) — still the relation instance cached before
the write. So `StudentHistory` records `['new' => <pre-update>, 'old' => <pre-update>]`: a result
change is logged as a no-op.

This is the audit trail for grade changes, the single most integrity-sensitive mutation in the
system. It is currently unreliable in exactly the case it exists to record.

**Fix:** `$registration->refresh();`. Add a test asserting the history row's `new` differs from `old`
after a score change — the current bug would pass any test that only checks a history row exists.

---

## 2. P1 — Access control

The authorization model is the weakest part of the application, and the domain (student PII and
academic results) makes it the highest-value area to strengthen.

### 2.1 Authorization is effectively absent from the HTTP layer **[verified]**

Counts from the codebase:

- 78 controllers under `app/Http/Controllers`. **Zero** contain `authorize(`, `Gate::` or `->can(`.
- 15 form requests. **Two** override `authorize()` (`DepartmentSessionRequest`,
  `CurriculumImportRequest`); the rest inherit the permissive default.
- `routes/web.php` applies `->can(...)` to exactly **5** routes (student delete, registration delete,
  result update, and three import deletes).
- 4 policies exist, and every method reduces to `$user->isAdmin()`.

Everything else in `routes/web.php` sits behind a bare `['auth']` group. Any authenticated user can
read, export, download and mutate any record in the system.

### 2.2 Four of six roles carry no privileges **[verified]**

`App\Enums\Role` defines `SUPER_ADMIN`, `ADMIN`, `DESK_OFFICER`, `EXAM_OFFICER`, `DATABASE_OFFICER`,
`USER`. The only checks in the codebase are `isAdmin()` (super-admin or admin) and `isSuperAdmin()`.
`DESK_OFFICER`, `EXAM_OFFICER`, `DATABASE_OFFICER` and `USER` are therefore **indistinguishable at
runtime** — the role column is documentation, not enforcement. A `USER` has the same effective
access as a `DATABASE_OFFICER`: everything except the five `->can()` routes.

### 2.3 Department scoping is presentational only **[verified]**

`DepartmentListData::forUser()` (`app/Data/Department/DepartmentListData.php:35-48`) restricts the
department dropdown to `$user->departments()` for non-admins. That is the *only* use of the
`UserDepartment` relation anywhere in `app/` — grep finds it in `User.php` and the model file alone.

No server-side check enforces it. `DownloadStudentsByDepartmentSessionRequest` validates
`department.id` with `exists:departments,id` and nothing more. An exam officer scoped to one
department can POST any `department.id` and pull another department's full result set. The same
pattern repeats across the ~20 download/export endpoints, which all accept a department or session id
in the body and all share this shape of request class.

This is a horizontal privilege escalation reachable by editing one form field.

### 2.4 Open public registration on an academic records system **[verified]**

`routes/auth.php` exposes `GET/POST register` under `guest`.
`app/Http/Controllers/Auth/RegisteredUserController::store` validates name/email/password, creates the
user, and calls `Auth::login($user)` — with:

- **no email-domain restriction**, even though `User::inDomain()` and `config('rp.domain')`
  (`ebsu.edu.ng`) exist for exactly this purpose and are already used by `isAdmin()`;
- **no email verification** — `User` does not implement `MustVerifyEmail`, and
  `ProfileController:23` checks for that interface, so the verification UI is dead code;
- **no approval step** — the account is live and logged in immediately;
- role defaulting to `user` (`0001_01_01_000000_create_users_table.php:22`).

Chained with §2.1: anyone on the internet can self-register and immediately read and export the
student records of the entire institution. This is the most serious finding in the review.

**Recommended direction for §2.1-2.4** (deliberately incremental, since a full RBAC rewrite is not
warranted):

1. **Immediate:** disable or gate public registration — remove the routes and provision via Filament,
   or at minimum require `inDomain()` plus admin approval before the account becomes usable.
2. Add a `Gate::before` deny-by-default posture, or apply an explicit `can:` middleware to every
   route group in `routes/web.php`, so new routes fail closed rather than open.
3. Introduce a `ScopedToDepartment` form-request trait or a route middleware that validates the
   submitted department against `$user->departments()` for non-admins. Applying it to the shared
   download/export request base classes covers most of the surface in one change.
4. Give the four inert roles real capabilities, driven by a policy matrix rather than `isAdmin()`.
5. Backfill feature tests asserting a non-admin is **denied** — the current suite has 10 controller
   tests for 78 controllers and does not cover negative authorization at all.

### 2.5 A live Sentry DSN is committed to `.env.example`

`.env.example` ends with a real-looking populated DSN
(`https://4ce4d145...@o4509213051125760.ingest.de.sentry.io/...`) and
`SENTRY_TRACES_SAMPLE_RATE=1.0`. A DSN is a write-only ingestion key, so exposure is not critical,
but a committed one lets anyone submit forged events into the project's issue stream and burn its
quota. `APP_DEBUG=true` in the same file is a poor default to copy into a new environment.

**Fix:** blank the DSN placeholder, drop the sample rate to something production-appropriate, and
rotate the key if the repository is not private.

---

## 3. P2 — Reliability and async architecture

### 3.1 Horizon and Redis are installed, but there are no queued jobs **[verified]**

There is **no `app/Jobs` directory**. `laravel/horizon`, `predis`, Reverb and a `queue:listen` process
in `composer dev` are all configured, but no application work is ever dispatched to a queue. All
background processing is instead done by `everyMinute` scheduled commands that each process **one
record per tick**:

- `ProcessRawExcelUploads` — `->first()`, one import per minute.
- `ProcessQueuedVettingCommand` — one *group* per tick (and never scheduled, §1.5).

This caps import throughput at 60 spreadsheets/hour regardless of available workers, and offers no
retries, no backoff, no failure visibility, and no `failed_jobs` record. A vetting group of 400
students runs as one unbounded synchronous command with `withoutOverlapping()`'s default 24-hour lock.

Related: `VettingController::create` (line 25) wraps `Artisan::call('rp:vet', ...)` in `defer()`. That
runs the full vetting battery in the web worker after the response is flushed — occupying a php-fpm
child for the duration, with no retry and no surfaced failure. `defer()` is intended for short
fire-and-forget work, not a multi-step academic check.

**Fix:** convert both vetting paths and Excel processing into queued jobs with `$tries`/`backoff`,
dispatched from the controllers. The scheduled commands become thin dispatchers, or disappear.
Horizon then provides the throughput and observability it was installed for.

### 3.2 Long-running processes have no failure handling — records strand mid-state **[verified]**

`Services\Vetting\Vetting::vet()` sets the event to `VETTING`, loops the steps, then updates status.
There is **no try/catch anywhere in the vetting path** (verified across `Vetting.php` and
`ProcessQueuedVettingCommand.php`). If any one step throws:

- the `VettingEvent` stays in `VETTING` permanently;
- in the group command, the enclosing `VettingEventGroup` also stays in `VETTING`, and the remaining
  students in the batch are never processed;
- since only `QUEUED` groups can be deleted, the stuck group cannot be cleared through the UI.

This is the same stranding failure mode as §1.4, in a second subsystem. The system has several
status-machine workflows and no general answer for "a transition failed halfway."

**Fix:** wrap per-item work so one failure marks that item `FAILED` and lets the batch continue;
add a terminal state and a sweeper for items stuck in a transient state beyond a timeout. Job
retries (§3.1) give most of this for free.

### 3.3 Multi-table writes are not transactional **[verified]**

A grep for `DB::transaction|beginTransaction` across `app/` returns **one hit** — the broken manual
one in `ClearanceController` (§1.3).

Nothing else is atomic, including the operations that most need to be:

- Excel import promotion (`Actions/Imports/Excel/*`) — moves `Raw*` rows into `Result`/`Registration`
  in batches; a mid-run failure leaves a half-imported result set with no marker of where it stopped.
- `Registration::updateRegistrationAndResult` — writes `registrations` then `results` as separate
  saves (`app/Models/Registration.php:86-99`); a failure between them leaves a credit unit updated
  against an unrecomputed grade.
- Student deletion, which spans the student record, history and mail rows.

On an academic records system, a partially applied result import is worse than a failed one, because
nothing signals that it happened.

**Fix:** wrap each Action's write path in `DB::transaction()`. Prefer the closure form throughout.

### 3.4 The portal API client has no timeout, no retry, and a production-unsafe assertion **[verified]**

`app/Http/Clients/ApiClient.php`:

- **No timeout** on the `Http::acceptJson()->baseUrl(...)->get(...)` chain (line 32). Laravel's default
  is 30s connect but no overall cap in older configs; a hung portal stalls the scheduled command,
  which holds `withoutOverlapping()`'s lock and blocks *all* subsequent import ticks.
- **No retry/backoff** for a flaky third-party endpoint that this system depends on entirely.
- **`assert(array_key_exists('data', $response))` at line 42.** Assertions are compiled out under
  production's `zend.assertions=-1`. When the portal returns a payload without `data`, the guard
  vanishes and line 44 passes `null` to `array_map`, raising a `TypeError` rather than the intended
  clear failure. `assert()` is used as a control-flow guard in several other places
  (`ProcessRawExcelUploads:34`, most controllers' `assert($user instanceof User)`) — the pattern is
  worth reviewing wherever a false assertion would corrupt rather than merely mislead.
- Both catch blocks collapse typed exceptions into generic `Exception`, discarding the status code
  and response body that would make a portal failure diagnosable.

**Fix:** add `->timeout(...)->retry(3, 200, throw: false)`; replace the assertion with an explicit
`throw`; preserve the original exception via `previous:`.

---

## 4. P3 — Data layer and performance

### 4.1 N+1 queries inside vetting steps **[verified]**

- `app/Actions/Vetting/VerifyCoursesCreditUnit.php:32-33` — `Registration::query()` inside a
  `foreach` over registrations.
- `app/Actions/Vetting/VerifyFailedCourses.php:36` — `Registration::query()->find(...)` per failed
  course.

Vetting runs every step against every student in a batch, so this multiplies: a 400-student group
with 40 registrations each issues tens of thousands of avoidable queries. Given the batch already
runs as one long synchronous command (§3.1), this is a material contributor to its duration.

`Model::shouldBeStrict(App::isLocal())` (`AppServiceProvider:86`) means `preventsLazyLoading` is
**off** outside local, so N+1s introduced later will not be caught in CI or staging either.

**Fix:** eager-load or pre-index the registrations into a keyed collection before the loop. Consider
enabling strict mode in the testing environment so the arch/feature suite catches regressions.

### 4.2 Index coverage is unverified across half the schema

31 of 74 migrations declare an `index()` or `unique()`. `foreignIdFor(...)->constrained()` yields an
index, but the several `foreignIdFor(...)->nullable()` columns without `constrained()` — e.g.
`lecturer_id` in `create_results_table` — do not. Given that the hot queries filter by
department + session + level across `registrations`/`results`, the composite indexes those need
should be confirmed against real query plans rather than assumed.

**Fix:** run `EXPLAIN` on the main report/summary/export queries against a production-sized dataset
and add composite indexes where warranted. This is measurement work, not a blind index-everything
pass.

### 4.3 SQLite in tests vs MySQL in production

`phpunit.xml` and CI both run on SQLite `:memory:`; `.env.example` targets MySQL. The `selectRaw`
aggregates in `ImportEvent` (`count(case when ... end)`) are portable, but JSON column behaviour
(`results.scores` is `json`), strict-mode semantics, collation-dependent ordering and `unique`
constraint interaction with `NULL` all differ. Tests can pass against behaviour production does not
have.

**Fix:** add a CI job running the suite against a MySQL service container, matching the production
version. Keep SQLite for the fast local loop.

---

## 5. P4 — CI and reproducibility

`.github/workflows/ci.yml` gates the right things (frontend build, Pint, PHPCS, PHPStan, tests) but
several jobs resolve dependencies rather than installing pinned ones.

### 5.1 The committed `yarn.lock` is ignored by CI **[verified]**

`package.json` declares `"packageManager": "yarn@1.22.22..."` and `yarn.lock` (149 KB) is committed
and is the only lockfile present. CI runs **`npm install --force`** (frontend job). This ignores
`yarn.lock` entirely, resolves fresh versions, and `--force` suppresses the peer-dependency errors
that would otherwise reveal the mismatch. CI is building a dependency tree no developer has.

**Fix:** `yarn install --frozen-lockfile`. If the project has actually moved to npm, commit
`package-lock.json`, drop `yarn.lock` and the `packageManager` field, and use `npm ci`.

### 5.2 Quality tools are installed unpinned

- `pint` job: `composer global require laravel/pint` — installs the newest Pint, not the version in
  `composer.lock`. A Pint release changing a rule breaks CI on an untouched codebase.
- `phpcs` job: `composer require slevomat/coding-standard --dev` — mutates `composer.json` during the
  run and re-resolves.
- `frontend` job: `composer require tightenco/ziggy` for the same reason.

All three are already dev dependencies in `composer.json`.

**Fix:** `composer install` and invoke `vendor/bin/{pint,phpcs}`, matching the documented local
commands in `CLAUDE.md`.

### 5.3 PHP version drift **[verified]**

Commit `7f5ec186` is titled "Upgrade framework and tooling for PHP 8.5", but `composer.json` requires
`"php": "^8.4"`, all five CI jobs pin `php-version: '8.4'`, and `CLAUDE.md` says PHP 8.4. Nothing
tests 8.5. Either the constraint and CI should move to 8.5, or the commit title overstates the work —
worth resolving so the intended baseline is unambiguous.

### 5.4 PHPStan suppresses an entire class of null-safety bugs **[verified]**

`phpstan.neon` reaches level 9 partly on the strength of these `ignoreErrors` patterns:

```
'#^Cannot access property \$\w+ on App\\Models\\\w+\|null\.$#'
'#^Cannot call method \w+\(\) on App\\Models\\\w+\|null\.$#'
```

These are not noise — they are precisely the "this can be null and you did not check" diagnostics,
suppressed across **every model in the application**. Two further patterns silence null being passed
where a `User` or a model is required. Combined with `treatPhpDocTypesAsCertain: false` and
`checkExplicitMixed: false`, the level-9 badge overstates the guarantee.

The Larastan `int<0, max>` suppression is well-justified and documented in a comment — that one is
fine and shows the right instinct.

**Fix:** move these from blanket `ignoreErrors` into `phpstan-baseline.neon` so the existing
violations are frozen with a known count while new ones fail the build. Then burn the baseline down.

### 5.5 Test coverage is concentrated away from the risk

184 test files, but the distribution is uneven relative to where the danger is: `tests/Unit` has 56
files (largely `Data` classes — the lowest-risk layer), while `tests/Feature/Controllers` has **10**
for 78 controllers. Given §2.1, the untested layer is the one enforcing access to student records.
No coverage threshold is enforced in CI.

**Fix:** prioritise feature tests that assert **denial** for non-admin and out-of-department users on
the download/export/update routes. That directly locks in the §2 fixes.

---

## 6. P5 — Structure and hygiene

- **`CLAUDE.md` is gitignored** (`.gitignore` last line) and untracked — verified via
  `git ls-files`. The project's own architectural guidance is not shared with the team or with CI.
  Given its quality, it should be committed.
- **`CLAUDE.md` has drifted from the code.** It states Laravel 12 (now `^13.0`) and describes
  `rp:process-queued-vetting` as scheduled when it is not (§1.5). Documentation describing behaviour
  the code does not have is how §1.5 stayed invisible.
- **A FormRequest lives in the controllers directory** — `app/Http/Controllers/Students/StudentDeleteRequest.php`,
  which is why `StudentController` uses it with no `use` statement. It belongs in `app/Http/Requests`.
- **`TestingServiceProvider` ships in `app/Providers`.** It is guarded by `runningUnitTests()`, so it
  is inert in production, but test-only macros in application code is why four `AssertableInertia`
  entries sit in the PHPStan baseline. Moving it under `tests/` removes both problems.
- **`.nvmrc` is gitignored** yet present, so Node version pinning is per-developer. CI hardcodes Node
  20 separately. Commit the file and read it in CI.
- **19 student-update controllers follow an identical shape** (validate → resolve user → call action →
  redirect). The repetition is consistent and readable, so this is low priority — but a shared base
  request or a single controller keyed by an `enum` of modifiable fields would remove ~19 near-copies.
- **Audit coverage is good where it exists, absent where it does not.** `StudentHistory` records
  field-level changes across the update actions (verified across 8+ actions), and that design is
  sound — subject to §1.6. But `spatie/laravel-activitylog` is invoked in only **2** files, and bulk
  exports/downloads of student PII are not logged at all. For an institution handling academic
  records, "who exported which department's results" is likely an audit requirement.

---

## 7. Suggested sequencing

Ordered by risk-reduction per unit of effort, not by severity alone.

| # | Item | Why first | Effort |
|---|---|---|---|
| 1 | Close public registration (§2.4) | Single highest exposure; a routes-file change buys time for the rest | Trivial |
| 2 | Fix the four migration `down()` methods (§1.1) | Data-destructive, isolated, no design decisions | Trivial |
| 3 | Fix `Role::creatable()` (§1.2) + audit existing roles | Wrong data accumulating daily | Trivial |
| 4 | Fix the three stranding/staleness bugs (§1.3, §1.4, §1.6) | Each is a few lines; each currently corrupts or hides state | Small |
| 5 | Schedule or queue `rp:process-queued-vetting` (§1.5) | A whole feature is inert in production | Small |
| 6 | Department scoping on download/export requests (§2.3) | Closes horizontal escalation; one shared trait covers ~20 endpoints | Medium |
| 7 | Deny-by-default authorization + negative feature tests (§2.1, §5.5) | Makes every future route fail closed | Medium |
| 8 | Pin CI dependencies, fix the yarn/npm split (§5.1, §5.2) | Makes every later change trustworthy | Small |
| 9 | Wrap Action write paths in transactions (§3.3) | Mechanical, high integrity payoff | Medium |
| 10 | Move PHPStan null suppressions to the baseline (§5.4) | Stops the bleeding; enables incremental cleanup | Small |
| 11 | Migrate background work to queued jobs (§3.1, §3.2) | Largest structural win, but wants the above stabilised first | Large |
| 12 | N+1 and index work (§4.1, §4.2) | Measure before optimising; cheaper once §11 lands | Medium |

Items 1-5 are roughly a day's work in total and eliminate every confirmed correctness bug plus the
worst security exposure. Items 6-7 are the substantive security work. Item 11 is the only entry that
warrants a design discussion before implementation.
