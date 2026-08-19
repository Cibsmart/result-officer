# Audit Trail

Design for a single, enforced audit trail replacing the two overlapping mechanisms in the codebase
today: `StudentHistory` and `spatie/laravel-activitylog`. Both are deleted at the end of §9.

Reference implementation: the `seed-hub` audit trail (`AuditLog` / `AuditAction` / `RecordAudit` /
`AuditContext` / `AuditLogController`). This design follows its shape and departs from it in three
places, each marked **[departs from seed-hub]**.

## 1. Verdict on the merge question

**Merge.** There is no case for keeping both, but the merged table must inherit `StudentHistory`'s
columns rather than seed-hub's thinner schema.

| Trail | Today | Disposition |
|---|---|---|
| `StudentHistory` | 16 write sites; typed `field`, `old`/`new`, mandatory `remark`, `source`, FK to `DBMail`. **No reader** — `Student` has no relation to it, and the History tab is a `href="#"` stub (`resources/js/pages/students/show/partials/studentDetails.vue:21`). | **Absorbed.** Its columns become first-class columns on `audit_logs`. Backfilled, then dropped. |
| `spatie/laravel-activitylog` | 3 call sites (`ProfileController` ×2, `ClearanceController` ×1). No `LogsActivity` trait anywhere, no published config, no reader, no UI. | **Deleted.** Package removed, migrations reversed. |
| `ImportEvent` / `ExcelImportEvent` | Already carry `user_id` and a full `ImportEventStatus` lifecycle. | **Kept as-is, linked not duplicated.** See §6. |

The trap to avoid is treating `StudentHistory` as the redundant one because it is unread. It is the
only trail carrying *authorization evidence* — a `remark` and a `DBMail` foreign key, the
departmental memo that sanctioned the amendment. That is domain data, not telemetry. Activitylog
cannot express it, which is why the two systems never converged.

The seed-hub precedent for keeping a second table (`status_transitions`, documented as running
"alongside (not replacing) the audit trail") does not apply here. That table exists because status
timelines are queried as a first-class sequence. `StudentHistory` is queried as an audit trail and
nothing else.

## 2. Decisions

| # | Decision | Consequence |
|---|----------|-------------|
| 1 | **One table, `audit_logs`.** | §4. `student_histories` and `activity_log` both dropped in §9. |
| 2 | **Memo authorization generalizes.** `db_mail_id` and `remark` are columns on `audit_logs`, not JSON, and are available to any audited action — not only student amendments. | Answers the open question in `filament-migration.md` (curriculum credit-limit edits, phase 4). §4.2. |
| 3 | **Two morph axes: `subject` and `scope`.** `subject` is the record that changed; `scope` is the record it belongs to. **[departs from seed-hub]** | Preserves `StudentHistory`'s `student_id` + `modifiable_*` split. Makes "this student's history" an indexed query, not a JSON scan. §4.1. |
| 4 | **Enforced immutability.** The model refuses `updating` and `deleting`; there is no `$fillable` path and no `updated_at`. | §5.2. Guarded by an arch-style test, not by convention. |
| 5 | **One dotted `AuditAction` enum** replaces the `RecordActionType` × `StudentModifiableField` pair. | ~20 cases, one filter dropdown, one `label()`. §5.1. |
| 6 | **Imports are audited at event level, never row level.** | A department-session results import writes one audit row, not thousands. §6. |
| 7 | **The trail is readable.** A filterable admin screen, a per-student History tab, and a CSV export. | §7. Without a reader this repeats the current failure. |

### Scope

**In:** student and result amendments (the 16 existing sites), clearance and graduand finalization,
imports (portal + Excel).

**Out, deliberately:** Filament admin resource edits — `Course`, `Department`, `Faculty`,
`ProgramCurriculum*`, `Session`, `User`. These are currently unaudited and remain so **here**, but
not permanently: `filament-migration.md` replaces every one of those resources with Inertia
controllers, and each replacement calls `RecordAudit` as it is written. Retrofitting audit onto the
Filament resources first would be work thrown away. The gap is real in the meantime —
`ProgramCurriculum` edits silently change who can graduate (see `filament-migration.md`,
"highest-consequence pivot") and `users.role` writes are the privilege-escalation path (see
`improvements.md` §1.2 on the swapped `Role` values) — which is an argument for not letting that
migration slip, not for double-building. Sequencing in `roadmap.md`, Waves F and G.

**Out, by overlap:** vetting. The vetting redesign (`vetting-redesign.md` decisions 3, 4, 8) builds
immutable runs, findings and waiver records of its own. Vetting writes to `audit_logs` only for the
waiver grant — one case, added when that subsystem lands.

---

## 3. Prerequisite: the stale-audit bug

`app/Actions/Results/ResultUpdateAction.php:32` calls `$registration->fresh();` and discards the
return. `fresh()` returns a new instance; it does not mutate the receiver. The next statement builds
the audit record's `new` value from `$registration->getUpdateData()` on the *unrefreshed* model, so
every grade change is recorded as `['new' => <pre-update>, 'old' => <pre-update>]`.

Grade amendment is the most integrity-sensitive mutation in the system, and its audit trail records
nothing. Fix is `$registration->refresh();`. Ship this **before** anything else in this document —
it is independent of the merge and every historical row it produced is already worthless.

The test must assert `new !== old` after a score change. The current bug passes any test that only
asserts a history row exists, which is exactly what
`tests/Feature/Actions/Students/ResultDeleteActionTest.php` and its siblings do.

Backfill implication: rows with `field = result`, `action = update` and `new == old` cannot be
repaired. Backfill (§8) copies them with a `context` marker `{"integrity": "stale-old-value"}` so
they are visibly untrustworthy rather than silently wrong.

---

## 4. Schema

```
audit_logs
  id
  user_id           FK users, nullable            -- null only for system/scheduled actors
  action            string, indexed               -- AuditAction
  source            string                        -- RecordSource: user|portal|excel|legacy|system
  subject_type      string, nullable  \ nullableMorphs('subject')
  subject_id        bigint, nullable  /            -- the record that changed
  scope_type        string, nullable  \ nullableMorphs('scope')
  scope_id          bigint, nullable  /            -- the record it belongs to (usually Student)
  old               json, nullable
  new               json, nullable
  context           json, nullable                 -- everything else
  remark            text, nullable
  db_mail_id        FK db_mails, nullable
  legacy_student_history_id  bigint, nullable, unique   -- backfill idempotency; dropped in §9
  created_at        timestamp                      -- no updated_at

  index (scope_type, scope_id, created_at)
  index (user_id, created_at)
  index (action, created_at)
```

### 4.1 Why two morph axes

`StudentHistory` already has this shape — `student_id` plus `modifiable_type`/`modifiable_id` — and
it is correct. A grade change has `subject = Registration`, `scope = Student`. Collapsing to
seed-hub's single `subject` morph forces a choice between "show me everything about this student"
and "show me everything that happened to this registration"; both are wanted.

`scope` is nullable and generalizes past students: a curriculum course change scopes to the
`ProgramCurriculum`, an import scopes to a `Department` or null.

Rule: **`scope` is never the same record as `subject`.** When a student's own column changes,
`subject = Student` and `scope` is null — the student is already the subject. `Student::auditLogs()`
unions on both axes (§7.2).

### 4.2 Why `remark` and `db_mail_id` are columns

Per decision 2. They are queried (`whereNotNull('db_mail_id')` — "show me every amendment backed by
a memo"), they are constrained (a real FK to `db_mails`, which JSON cannot give), and they will
become *required* for some actions. `AuditAction::requiresMemo(): bool` drives that, letting the
recording action reject an unsanctioned change rather than logging one.

### 4.3 `old` / `new` split out of `context`

`StudentHistory` packs `['old' => …, 'new' => …]` into `data`; seed-hub has only `context`. Splitting
them into their own columns makes the diff renderer total rather than defensive, and leaves `context`
for genuinely free-form payload (import counts, allocation changes, reasons). **[departs from
seed-hub]**

---

## 5. Write side

### 5.1 `App\Enums\AuditAction`

Dotted `verb-object` values with `label()`, following seed-hub exactly:

```php
case StudentNameUpdated        = 'student.name-updated';
case StudentEmailUpdated       = 'student.email-updated';
… one per StudentModifiableField case …
case StudentDeleted            = 'student.deleted';
case ResultUpdated             = 'result.updated';
case ResultDeleted             = 'result.deleted';
case StudentCleared            = 'student.cleared';
case GraduandFinalized         = 'graduand.finalized';
case PortalImportCompleted     = 'import.portal-completed';
case PortalImportFailed        = 'import.portal-failed';
case ExcelImportCompleted      = 'import.excel-completed';
case ExcelImportFailed         = 'import.excel-failed';
```

Plus:
- `label(): string` — display text, as seed-hub.
- `requiresMemo(): bool` — see §4.2.
- `static forStudentField(StudentModifiableField $field): self` — keeps the existing typed enum
  meaningful and gives the backfill (§8) a total mapping.

`RecordActionType` (`create`/`update`/`delete`) is deleted; the verb is in the action. `RecordSource`
survives unchanged as the `source` column.

### 5.2 `App\Models\AuditLog`

`final` (per house convention; seed-hub's is not). No `$fillable`; construction goes through
`RecordAudit` only. `public const UPDATED_AT = null;`. In `booted()`, `updating` and `deleting`
listeners throw. Casts: `action`, `source`, and `old`/`new`/`context` to `array`. Relations
`user()`, `dbMail()`, `subject()`, `scope()` with full `@phpstan-return` generics per house style.

### 5.3 `App\Actions\Audit\RecordAudit`

One injectable action, `execute()` with named arguments (house convention — seed-hub uses `handle()`):

```php
public function execute(
    AuditAction $action,
    ?Model $subject = null,
    ?Model $scope = null,
    ?User $user = null,
    RecordSource $source = RecordSource::USER,
    ?array $old = null,
    ?array $new = null,
    array $context = [],
    string $remark = '',
    ?DBMail $dbMail = null,
): AuditLog
```

Throws when `$action->requiresMemo()` and `$dbMail` is null. Constructor-injected into the calling
Actions, as seed-hub does — not a facade or helper, so it is trivially faked in tests.

### 5.4 Migrating the 16 call sites

Each `StudentHistory::createNewUpdate(...)` becomes a `$this->recordAudit->execute(...)`. The
argument shapes already line up; `updatedField:` becomes `action: AuditAction::forStudentField(...)`,
and `data:` splits into `old:`/`new:`. The Actions gain a constructor.

Public signatures of the Action `execute()` methods do not change, so the controllers under
`app/Http/Controllers/Students/Updates/*` and their tests need no edits beyond the
`assertDatabaseHas(StudentHistory::class, …)` assertions, which move to `AuditLog::class`.

`StudentDeleteAction` currently logs a delete as `field: REGISTRATION_NUMBER`, which is a workaround
for the field-enum being mandatory. It becomes `AuditAction::StudentDeleted` — the misfiling goes
away with the enum.

---

## 6. Imports

`ImportEvent` and `ExcelImportEvent` already record who started an import and its full status
lifecycle. They are not replaced and not duplicated. Instead:

1. **One terminal audit row per event.** When `ProcessPortalData` / `ProcessRawExcelUploads` reaches
   `COMPLETED` or `FAILED`, record one `AuditLog` with `subject` = the import event, `user` = the
   event's user, `source` = `PORTAL` or `EXCEL`, and `context` = the record counts (from
   `ImportEvent::getCounts()` / `ExcelImportEvent::rawRecordCount()`) plus the failure message.
2. **Attribution, not enumeration, on promoted rows.** Promoting `Raw*` rows into domain models does
   not write per-row audit entries. The audit row above is the anchor; the `Raw*` tables and their
   `import_event_id` are the row-level detail, and they already exist.
3. **Exception: an import that overwrites an existing result.** That is an amendment, and it is
   indistinguishable in consequence from a manual grade change. Those rows *do* get individual
   `AuditAction::ResultUpdated` entries with `source = PORTAL|EXCEL` and no memo. Volume is bounded
   by how many results an import actually overwrites, which should be near zero — if it is not, the
   trail has surfaced a real problem.

Point 3 is the one that needs care during implementation: the promotion path must distinguish insert
from overwrite. If it currently cannot, that is a prerequisite finding, not something to skip.

---

## 7. Read side

Without this the new trail repeats the old one's fate.

### 7.1 Admin trail screen

`AuditLogController` (invokable index) → `Inertia::render('audit/index', new AuditTrailPage(...))`
per the ViewModel convention. Filters: action, user, date range, subject type, and free-text on
`remark`. Paginated 20, `withQueryString()`, `latest('id')`.

Props go through a `App\Data\AuditLogData` DTO rather than seed-hub's inline `->through(fn …)` array
map — spatie/laravel-data is the Inertia boundary in this codebase. **[departs from seed-hub]**

Context formatting reuses seed-hub's `AuditContext` logic — flattening arbitrary nested arrays into
`{label, value}` pairs so nested payloads do not render as `[object Object]` — placed at
`app/Values/AuditContext.php` (this repo has no `app/Support`).

### 7.2 Student History tab

Fills the `href="#"` stub in `studentDetails.vue:21`. Backed by a `Student::auditLogs()` relation
unioning `scope = $student` with `subject = $student` (§4.1), newest first, rendering the `old`→`new`
diff, the remark, the memo title and date from `DBMail`, and the actor.

### 7.3 Export

`AuditLogExportController` streaming CSV over the same filtered query, with `context` collapsed to a
single line via `AuditContext::summary()`. Reuse the existing openspout wrapper
(`App\Exports\ResultsExport` pattern) rather than hand-rolling CSV.

### 7.4 Policy

`AuditLogPolicy` — `viewAny` on an explicit permission. Department-scoped users see only rows whose
`scope` resolves to a student in their department; the trail must not become a cross-department
data leak. `view` inherits the same scoping. There is no `update` or `delete` — the model refuses
both (§5.2).

---

## 8. Backfill

Whether production rows exist is unresolved (§10, Q1), so the backfill is written to be safe either
way: idempotent, reversible, and skippable.

`rp:backfill-audit-logs {--dry-run} {--chunk=1000}`:

- Chunks `student_histories` by id, mapping each row: `student_id` → `scope` (or `subject` when the
  history's `modifiable_type` is `Student`), `field` + `action` → `AuditAction`, `data` → `old`/`new`,
  `remark`/`source`/`db_mail_id`/`user_id`/`created_at` carried across verbatim.
- Writes `legacy_student_history_id`, which is uniquely indexed. Re-running skips what it already
  moved. `--dry-run` reports the mapping histogram and any row whose `field`/`action` pair has no
  `AuditAction` — that count must be zero before the real run.
- Tags stale result rows per §3.
- Timestamps are inserted directly, bypassing model events; the immutability guard makes the
  ordinary path refuse writes with a set `created_at`, so the backfill uses the query builder.

The three `activity_log` rows are not migrated. They are two profile events and one clearance event,
and the clearance event is re-implemented properly in §6-adjacent work.

Old tables stay in place, unread, until §9.

---

## 9. Cutover

Ordered. Each phase is independently shippable and leaves CI green.

| Phase | Work | Gate |
|---|---|---|
| **0** | §3 — `refresh()` fix plus the `new !== old` test. Ship alone. | Independent, urgent. Do not wait for the rest. |
| **1** | Migration, `AuditLog`, `AuditAction`, `RecordSource` reuse, `RecordAudit`, `AuditLogFactory` in `tests/factories/`. Immutability tests. | Nothing calls it yet. |
| **2** | Migrate the 16 `StudentHistory` sites. Move test assertions. `student_histories` still exists, now written by nothing. | Full existing test suite green. |
| **3** | Backfill command + `--dry-run` on a production snapshot. | Zero unmapped rows. |
| **4** | Clearance (`ClearanceController` — replacing its `activity()` call) and graduand finalization. | Note `improvements.md` §1.3: this controller never rolls back its transaction. Fix that first, or the audit row outlives a failed clearance. |
| **5** | Imports, §6. Includes resolving the insert-vs-overwrite question in §6.3. | |
| **6** | Read side: trail screen, student History tab, export, policy. | The first point at which the trail is actually useful. |
| **7** | Delete `StudentHistory`, `RecordActionType`, and the `student_histories` table. Drop `legacy_student_history_id`. | Backfill verified in production. |
| **8** | `composer remove spatie/laravel-activitylog`; drop `activity_log` and its three migrations. | No `activity()` calls remain. |

Phases 7 and 8 are the ones that will be tempting to defer indefinitely. They are the whole point —
leaving two dead trails in place is the state this document exists to end.

## 10. Open questions

| # | Question | Blocks |
|---|---|---|
| 1 | Does production hold `student_histories` rows that must survive? Answered "not sure yet" — §8 is written to work either way, but the answer determines whether phase 3 is a real migration or a no-op. | Phase 3 |
| 2 | Can the import promotion path currently distinguish inserting a result from overwriting one (§6.3)? If not, that becomes a prerequisite. | Phase 5 |
| 3 | Which `AuditAction` cases return `true` from `requiresMemo()`? Recommend: `result.updated`, `result.deleted`, `student.deleted`, `student.registration-number-updated`, `student.program-updated` — the amendments that change an academic record's substance. | Phase 2 |
| 4 | **Resolved.** Do *not* retrofit audit onto the Filament resources — `filament-migration.md` deletes them. `ProgramCurriculum` and `users.role` are still the two highest-consequence unaudited write paths, but the coverage belongs in the replacement Inertia admin controllers, written in as they are built. See `roadmap.md` Wave G. | — |
| 5 | Retention: does the trail expire? Recommend never — academic records are contested years later, and the table is small (one row per amendment, not per read). | Phase 1 |
