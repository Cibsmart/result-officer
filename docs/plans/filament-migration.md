# Result Officer — Filament → Inertia Admin Migration Plan

**Date:** 2026-08-18 · **Branch:** `task/upgrade` (at `fcc52b23`)
**Status:** Plan only — nothing here has been implemented.

---

## 0. Scope and destination

Replace the Filament 3 admin panel with hand-built Inertia + Vue pages following the same
Controller → Query → Data → ViewModel → page.vue pattern the rest of the app already uses, then
remove `filament/filament` from the project entirely.

**Done means:** `app/Filament/` and `app/Providers/Filament/` deleted, `filament/filament` gone from
`composer.json`, no Livewire in the response cycle, every admin write routed through an Action in
`app/Actions/`, and every admin route covered by a policy and a negative feature test.

**Why, in one line:** the Filament pages are stock `EditRecord`/`CreateRecord` classes that write
straight to Eloquent, bypassing `app/Actions/` — so the same model has two write paths with different
validation, and only one of them is audited. `app/Filament/Resources/StudentResource.php` and
`resources/js/pages/students/` are already a live example of that duplication.

**A legitimate stopping point:** Phases 1–3 remove every resource that has domain rules or a
duplicate Inertia counterpart. If Phase 4 (curriculum) turns out to be more UI work than it is worth,
stopping after Phase 3 and keeping Filament for the curriculum drill-down alone is a defensible
outcome — it just means not collecting the Phase 5 teardown. Decide that at the Phase 3 boundary,
with the real cost of Phases 1–3 known, rather than now.

---

## 1. Inventory — what actually has to be rebuilt

Ten resources and four relation managers, 42 files, 1,472 LOC. Two models (`Program`, and the
`user_departments` pivot) have **no resource of their own** and exist only inside relation managers —
easy to miss when scoping.

| # | Filament resource | Model(s) | Ops today | Phase |
|---|---|---|---|---|
| 1 | `StudentResource` + 2 relation managers | `Student`, `SessionEnrollment`, `Program` (read) | C/U/D | 1 — delete |
| 2 | `SessionResource` | `Session` | C/U | 2 |
| 3 | `CourseResource` | `Course` | C/U | 2 |
| 4 | `FacultyResource` + `DepartmentsRelationManager` | `Faculty`, `Department` | C/U | 2 |
| 5 | `DepartmentResource` + `ProgramsRelationManager` | `Department`, **`Program`** | C/U | 2 |
| 6 | `RecordsUnitHeadResource` | `RecordsUnitHead` | C/U | 2 |
| 7 | `UserResource` + `DepartmentsRelationManager` | `User`, **`user_departments` pivot** | C/U | 3 |
| 8 | `ProgramCurriculumResource` | `ProgramCurriculum` | R only (`canCreate() === false`) | 4 |
| 9 | `ProgramCurriculumLevelResource` | `ProgramCurriculumLevel` | R only | 4 |
| 10 | `ProgramCurriculumSemesterResource` + `ProgramCurriculumCoursesRelationManager` | `ProgramCurriculumSemester`, `ProgramCurriculumCourse` | C/U/D | 4 |

Nothing else is customised: no widgets (`->widgets([])`), no custom pages, stock `Dashboard`, no
custom Livewire components, no Filament plugins.

### Coupling outside `app/Filament/`

| Location | Coupling | Removed in |
|---|---|---|
| `app/Models/User.php:8,17,76` | `implements FilamentUser`, `canAccessPanel()` | Phase 5 |
| `app/Enums/{Role,Gender,EntryMode}.php` | `implements HasLabel` | Phase 5 |
| `app/Services/Search/GlobalSearch.php:63,111,164,184` | 4 × `route('filament.admin.…')` | Phases 2 & 4 |
| `app/Data/Search/SearchResultData.php` | `external` flag exists only for Filament links | Phase 5 |
| `resources/js/components/search/CommandPalette.vue:60-65` | full page load for `external` results | Phase 5 |
| `app/Enums/SearchGroup.php:38-52` | non-student groups hidden from non-admins **because** Filament gated them | Phase 0 |
| `composer.json` `post-autoload-dump` | `@php artisan filament:upgrade` | Phase 5 |
| `public/js/filament/`, `public/css/filament/` | published assets | Phase 5 |
| `bootstrap/providers.php` | `AdminPanelProvider` registration | Phase 5 |

---

## 2. The target pattern

`students/index` is the reference implementation. Every migrated resource copies its shape, so each
phase is mechanical rather than a design exercise. Read these seven files once before starting:

- `app/Http/Controllers/Students/StudentController.php` — thin, resourceful, multi-method
- `app/Queries/StudentIndex.php` — search + filters + sort + stable tie-break, all from the query string
- `app/Data/Students/StudentIndexFilterData.php` — `fromRequest()`, nullable-or-defaulted, never 422s
- `app/Data/Students/StudentFilterOptionsData.php` — dropdown options, department-scoped per user
- `app/ViewModels/Students/StudentIndexPage.php` — the Inertia prop bundle
- `resources/js/pages/students/index/page.vue` + `partials/studentFilters.vue`
- `tests/Feature/Controllers/Students/IndexTest.php` — including `assertHasComponent`

Shared UI already exists and needs no new work: `components/tables/` (`BaseTable`, `SortableTH`,
`BaseTHead/TBody/TR/TD`), `components/pagination.vue`, `components/emptyState.vue`,
`components/badge.vue`, `components/AppPage.vue`, `components/ui/card`.

### Per-resource file set

For a resource with filters and full CRUD, expect ~10 files:

```
app/Http/Controllers/Admin/<Name>Controller.php        index/create/store/edit/update/destroy
app/Http/Requests/Admin/<Name>{Store,Update}Request.php
app/Queries/<Name>Index.php                            only if it needs search/filter/sort
app/Data/<Domain>/<Name>ListData.php                   the table row DTO
app/Data/<Domain>/<Name>IndexFilterData.php            only if it has filters
app/ViewModels/Admin/<Name>IndexPage.php
app/Actions/<Domain>/<Name>{Create,Update,Delete}Action.php
app/Policies/<Name>Policy.php
resources/js/pages/admin/<name>/index/page.vue  (+ partials/<name>Form.vue)
tests/Feature/Controllers/Admin/<Name>/{IndexTest,StoreTest,UpdateTest}.php
```

### Two non-negotiable rules

1. **Every write goes through an Action** taking named arguments, never a `$model->update($request->validated())`
   in a controller. This is the entire point of the migration; a port that skips it reproduces the
   defect in Vue.
2. **Every route carries an explicit policy check**, not just `auth` middleware. See Phase 0 — this is
   the one thing Filament was silently providing.

Conventions from `CLAUDE.md` apply unchanged: `declare(strict_types=1)`, `final`, full
`@phpstan-return` generics on relations, factories from `Tests\Factories`, and `npm run check-build`
+ `pint` + `phpcs` + `phpstan` green before any phase is considered done.

---

## 3. Phase 0 — Prerequisites

These are blockers. Doing any of Phases 1–4 without them either ports a known bug or opens an
authorization hole.

### 0.1 Fix `Role::creatable()` first — **blocker for Phase 3**

`app/Enums/Role.php:22-25` maps `DESK_OFFICER => 'EXAM OFFICER'` and `EXAM_OFFICER => 'DESK OFFICER'`
(`improvements.md` §1.2). Porting `UserResource`'s role dropdown before fixing this bakes the swap
into the new form. Fix the array (or derive it from `getLabel()`), then audit existing `users.role`
values, because every account created through that form is suspect.

### 0.2 Real authorization — **blocker for Phases 1–4**

Today the only gate on admin CRUD is `User::canAccessPanel()` → `isAdmin()`, enforced by Filament.
Once these pages move into `routes/web.php` under `['auth']`, **every authenticated user reaches
them** unless a gate is added. `improvements.md` §2.1 already flags the HTTP layer as having
effectively no authorization; this migration makes that latent problem load-bearing.

Before Phase 1:

- Add `App\Http\Middleware\EnsureUserIsAdmin` (or a `Gate::before` + `can:` middleware) and alias it
  in `bootstrap/app.php` — note `withMiddleware()` currently registers no aliases at all.
- Create `routes/admin.php`, wired in `bootstrap/app.php`, with the whole group under
  `['auth', 'admin']` and prefix `admin`.
- Add a policy per model as each phase lands. `app/Policies/` already has `UserPolicy` and
  `StudentPolicy` to extend.
- Re-derive `SearchGroup::isVisibleTo()` (`app/Enums/SearchGroup.php:44-52`) from the new policies.
  Its current `default => $user->isAdmin()` is a comment-documented proxy for "Filament gates this",
  and that reason disappears in Phase 5.

### 0.3 Free the `/admin` path

`AdminPanelProvider::panel()` claims `->path('admin')`. Change it to `->path('legacy-admin')` — one
line, and the panel id stays `admin` so all `filament.admin.*` route names in `GlobalSearch` keep
working. New Inertia routes then take `/admin/*` from Phase 1 onward without a URL collision, and
nothing has to move again at teardown.

### 0.4 Test scaffold

`tests/Feature/Controllers/` has 11 files for 78 controllers and no negative-authorization coverage
at all. Add one shared helper asserting "non-admin gets 403" and use it in every phase. This is the
cheapest guard against Phase 0.2 quietly regressing.

**Phase 0 exit:** `/legacy-admin` still works for admins, a non-admin hitting a stub `/admin` route
gets 403, and there is a passing test proving it.

---

## 4. Phase 1 — Delete `StudentResource`

**This phase is a deletion, not a rewrite.** `resources/js/pages/students/` already supersedes it:
`index/page.vue` with `studentFilters.vue`, `show/page.vue` with 15 per-field update forms under
`partials/updates/`, plus `partials/deletes/`. All of it routes through
`app/Http/Controllers/Students/Updates/*` → `app/Actions/Students/Updates/*` with remarks and audit;
the Filament form has `->required()` and nothing else.

Steps:

1. Confirm field parity between `StudentResource::form()` and the 15 update forms. The Filament form
   covers `registration_number`, names, `gender`, `date_of_birth`, `program_id`,
   `local_government_id`, `entry_session_id`, `entry_level_id`, `entry_mode` — each has a
   counterpart under `partials/updates/`. Confirm student **creation**: the Filament panel has
   `CreateStudent`, the Inertia side does not. If admins genuinely create students by hand (rather
   than via the portal/Excel imports), that one form is the only new work in this phase.
2. Delete `app/Filament/Resources/StudentResource.php` and its `Pages/` + `RelationManagers/`
   directories (6 files).
3. Verify no `filament.admin.resources.students.*` references remain — `GlobalSearch` points students
   at `students.show`, so it is unaffected.

**Bonus:** this deletes a latent bug. `StudentResource/Pages/EditStudent.php:24` falls back to
`getUrl('view')`, but `StudentResource::getPages()` registers no `view` page — that throws whenever
`previousUrl` is null.

**Effort:** hours, unless the create form is needed.

---

## 5. Phase 2 — Reference data

Five resources, two of which carry a nested child. Order is deliberate: the two flat ones first to
settle the pattern, then the nested ones.

| Order | Resource | Fields | Nested child | Notes |
|---|---|---|---|---|
| 2.1 | `Session` | `name` (exactly 9 chars) | — | Flattest possible. Establishes the pattern. |
| 2.2 | `Course` | `code`, `title`, `active` (bool) | — | Needs search; `GlobalSearch` link switches here. |
| 2.3 | `Faculty` | `name`, `code` | Departments | First nested case. |
| 2.4 | `Department` | `name`, `code`, `faculty_id` | **Programs** | `Program` has no resource — only this relation manager. |
| 2.5 | `RecordsUnitHead` | `name`, `is_current` (bool) | — | `is_current` is presumably exclusive — enforce "only one current" in the Action; Filament never did. |

Design decision to make once, at 2.3, and then apply to 2.4: **how nested children are presented.**
Filament renders relation managers as tabs below the edit form. The cheapest equivalent here is a
`show` page per parent with a child table plus inline create/edit — the shape
`students/show/page.vue` already uses with its tabs under `partials/tabs/`. Pick one approach at 2.3
and reuse it; do not invent a second one at 2.4.

Also in this phase:

- Switch `GlobalSearch::courses()` (`:111`), `::departments()` (`:164`) and `::faculties()` (`:184`)
  to the new route names and drop `external: true` on those three, so command-palette hits become
  Inertia visits instead of full page loads.
- `programCurriculaUrl()` (`:60-67`) stays on Filament until Phase 4.
- Add the nav entries in `resources/js/components/sidebar/Navigation.vue` behind an admin check —
  the sidebar currently has no admin section at all.

**Effort:** the bulk of the migration. Roughly 8–10 files per resource, less for the flat ones.

---

## 6. Phase 3 — Users and department assignment

Depends on Phase 0.1 (the role label swap) and is the resource where authorization matters most.

- `User`: `name`, `email`, `password` (create only), `role`. The `password` cast is already `hashed`
  (`app/Models/User.php:95`), so the Action assigns the plain value and lets the cast hash it — do not
  double-hash.
- `user_departments` pivot via `DepartmentsRelationManager`. This is what
  `StudentFilterOptionsData::departments()` reads to scope every department dropdown in the app, so
  it is the highest-consequence pivot in the system and currently has an unaudited edit path.
- `UserPolicy` already exists — extend it rather than adding a parallel gate.
- Deleting or demoting a user needs a guard against removing the last admin.

**Effort:** one resource plus a pivot, but the most test coverage of any phase.

---

## 7. Phase 4 — Curriculum drill-down

The only phase with genuine UI design work, and the only one with meaningful domain consequences.

Current implementation is a hand-rolled three-level drill-down held together by a query parameter:
`ProgramCurriculumResource` sets `->recordUrl(...)` to the Level resource, and both
`ProgramCurriculumLevelResource:39` and `ProgramCurriculumSemesterResource:60` filter with
`->modifyQueryUsing(fn ($query) => $query->where(..., request()->query('record')))`. Both are hidden
from navigation (`shouldRegisterNavigation() === false`).

Replace with proper nested routes:

```
/admin/curricula                                     ProgramCurriculum index (read, filterable)
/admin/curricula/{programCurriculum}                 levels
/admin/curricula/{programCurriculum}/levels/{level}  semesters + their courses
```

Model binding replaces the `request()->query('record')` filtering, which also fixes the current
behaviour where hand-editing the URL shows an unfiltered list.

Scope:

- `ProgramCurriculum` and `ProgramCurriculumLevel` are **read-only** today
  (`canCreate() === false`, index-only pages) — keep them read-only.
- `ProgramCurriculumSemester` is full CRUD: `semester_id`, `minimum_elective_count`,
  `minimum_elective_units`, `minimum_credit_units` (default 15), `maximum_credit_units` (default 24).
  **These values feed the `VerifySemesterCreditLimits` and `VerifyElectiveCourses` vetting steps** —
  editing them silently changes who can graduate, so this Action needs an audit trail and probably a
  remark field, matching the student update pattern.
- `ProgramCurriculumCourse` (course + `credit_unit` + `course_type`) is full CRUD nested one level
  deeper. Reuse whatever nested-child pattern Phase 2.3 established.
- Note `ProgramCurriculumSemesterResource:71` renders `minimum_elective_units.value` — verify whether
  that column is a `CreditUnit` cast or a plain int before copying the accessor.

Finally, switch `GlobalSearch::programCurriculaUrl()` to the new route and drop its `external: true`.

**Effort:** the largest single phase. Budget design time for the drill-down before writing code.

---

## 8. Phase 5 — Teardown

Only after Phases 1–4. Each item is mechanical.

1. `rm -rf app/Filament app/Providers/Filament`
2. Remove `AdminPanelProvider::class` from `bootstrap/providers.php`
3. `composer remove filament/filament`
4. Remove `"@php artisan filament:upgrade"` from `post-autoload-dump` in `composer.json`
5. `app/Models/User.php` — drop `implements FilamentUser`, the two `use` statements, and
   `canAccessPanel()` (`:8-9,17,76-83`)
6. `app/Enums/{Role,Gender,EntryMode}.php` — drop `implements HasLabel` and the `use` line. **Keep
   `getLabel()`** — `StudentFilterOptionsData::forUser()` calls `$gender->getLabel()` directly. If
   you want the contract back, define a local `App\Contracts\HasLabel`.
7. `rm -rf public/js/filament public/css/filament resources/views/vendor/filament` (if present)
8. `app/Data/Search/SearchResultData.php` — remove the `external` field and its constructor default
9. `resources/js/components/search/CommandPalette.vue:60-65` — remove the full-page-load branch
10. `app/Enums/SearchGroup.php:35-52` — rewrite the comment and re-derive `isVisibleTo()` from policies
11. Check `config/` for a published `filament.php` (none found at time of writing) and
    `resources/views/vendor/` for overrides
12. Run `composer ide-helper` and regenerate TypeScript types
13. Full CI pass: `npm run check-build`, `pint --test`, `phpcs`, `phpstan`, `php artisan test --parallel`

**Effort:** under a day.

---

## 9. Sequencing and effort

Estimates are rough and assume the Phase 2 pattern settles on the first resource.

| Phase | Work | Blocks on | Effort |
|---|---|---|---|
| 0 | Role fix, admin middleware + `routes/admin.php`, policies scaffold, `/legacy-admin` path, negative-auth test helper | — | 1–2 days |
| 1 | Delete `StudentResource` (+ create form if actually needed) | 0 | Hours |
| 2 | Session, Course, Faculty(+Departments), Department(+Programs), RecordsUnitHead | 0, 1 | 1.5–2 weeks |
| 3 | User + department pivot | 0.1, 2 | 3–4 days |
| 4 | Curriculum drill-down (3 levels + nested courses) | 2 | 1–1.5 weeks |
| 5 | Teardown | 1–4 | <1 day |

**Total: roughly 4–6 weeks of focused work.**

### Ordering against `improvements.md`

Do **not** start Phase 2 before `improvements.md` items 1–5 (the P0 correctness bugs and the open
public-registration hole) are closed. Those are a day's work in total and several of them —
`Role::creatable()`, the migration `down()` methods, open registration — sit directly under this
migration's blast radius. Phase 0 of this plan overlaps substantially with `improvements.md` §2.1
(deny-by-default authorization), so doing them together is cheaper than doing either alone.

### Filament version decision

`composer.json` pins `filament/filament: ^3.2`; v4 is released. Given a 4–6 week migration, **pin to
`^3.2` and skip the v4 upgrade** — paying for a major upgrade of code scheduled for deletion is pure
waste. If this plan slips past ~6 months, revisit.

---

## 10. Open decisions

| # | Decision | Needed by |
|---|---|---|
| 1 | Do admins actually create students by hand, or only via portal/Excel import? Determines whether Phase 1 is pure deletion. | Phase 1 |
| 2 | Nested-child UI: parent `show` page with child table, or a separate child index route? Pick once, reuse everywhere. | Phase 2.3 |
| 3 | Do the four inert roles (`improvements.md` §2.2) get real capabilities now, or does everything stay `isAdmin()`-gated? Cheaper to answer while writing the policies than to retrofit. | Phase 0.2 |
| 4 | Should curriculum credit-limit edits require a remark + audit entry like student updates do? Recommended yes — they change graduation outcomes. | Phase 4 |
| 5 | Keep `/admin` for the new pages (with a redirect from `/legacy-admin`), or settle on a different prefix? | Phase 0.3 |
