# Ziggy → Laravel Wayfinder Migration Plan

**Date:** 2026-08-18 · **Branch surveyed:** `task/upgrade` (at `ca3a6021`)
**Last recount:** 2026-08-18, after the student list search/filter/sort work.
**Status:** Plan only — nothing in this document has been implemented.
**Estimated effort:** ~1–1.5 developer-days, spread over 8 commits.

---

## 0. Summary

Ziggy's footprint in this codebase is **four wiring points and 164 call sites**. There is no
custom Ziggy configuration, no route-name filtering, no `Ziggy` object shared through
`HandleInertiaRequests`, and — critically — **not one dynamically-computed route name**. Every
call passes a string literal, which makes the rewrite mechanical rather than exploratory.

The cost is breadth, not depth: 92 files change, but almost all of them change in the same way.

| Metric | Count |
|---|---|
| Ziggy wiring points | 4 |
| `route('name', …)` URL-generating calls | 145 |
| `route().current(…)` calls | 19 (all in one file) |
| **Total `route(` tokens in `resources/js`** | **164** |
| Files touched | 92 |
| Distinct route names referenced from the frontend | 96 |
| Dynamically-named `route(...)` calls | **0** |
| PHP-side `route()` calls | 44 files — **unaffected** |

### Why do it

- The client stops downloading a 162-route manifest on every page load (`@routes` in
  `resources/views/app.blade.php:41` inlines the whole route list into the HTML document).
- Route parameters become type-checked at build time instead of failing at runtime.
- HTTP method and URL travel together, which removes a class of latent bug this survey already
  found four instances of (§3).
- First-party Laravel package, aligned with the direction the Laravel starter kits have taken.

### Why it might wait

- **Wayfinder is still `v0.1.21`** — pre-1.0, breaking changes are permitted by semver. It does
  declare `illuminate/* ^11.0|^12.0|^13.0`, so Laravel 13 is supported.
- `route().current()` has **no Wayfinder equivalent**; §5 designs the replacement.
- `task/upgrade` already carries a framework upgrade, an Excel library swap, a PDF library swap
  and an unmerged search feature. Landing this on top makes an already-large branch harder to
  review or revert. **Recommendation: merge `task/upgrade` first, then do this on its own branch.**

---

## 1. Current state

### 1.1 The four wiring points

| # | File | What it does |
|---|---|---|
| 1 | `resources/views/app.blade.php:41` | `@routes` — inlines the route manifest into every response |
| 2 | `resources/js/app.ts:6,36` | `import { ZiggyVue } from '../../vendor/tightenco/ziggy'` + `.use(ZiggyVue)` — registers the global `route()` helper |
| 3 | `resources/js/types/global.d.ts:3,16,21` | Declares `const route` globally and on `ComponentCustomProperties` |
| 4 | `vite.config.js:38` + `tsconfig.json:16` | Alias `ziggy-js` → `vendor/tightenco/ziggy` |

Plus `composer.json:30` (`tightenco/ziggy: ^2.2`, locked at `v2.6.3`) and
`.github/workflows/ci.yml` (`Install Ziggy` step in the `frontend` job).

Because `route()` is registered globally, **no file currently imports it**. Every one of the 91
files will need at least one new import line — that is the single largest mechanical cost.

### 1.2 Call sites by area

| Area | Files | `route(` calls |
|---|---|---|
| `components/sidebar/Navigation.vue` | 1 | 38 |
| `pages/download` | 19 | 22 |
| `pages/students` | 20 | 21 |
| `components/*` (excl. sidebar) | 7 | 9 |
| `pages/auth` | 6 | 9 |
| `pages/reports` | 6 | 9 |
| `pages/finalResults` | 3 | 8 |
| `pages/export` | 4 | 7 |
| `pages/results` | 3 | 6 |
| `pages/summary` | 3 | 6 |
| `pages/vetting` | 4 | 6 |
| `pages/graduands` | 5 | 5 |
| `pages/settings` | 2 | 4 |
| `layouts/auth` | 3 | 3 |
| `layouts/settings` | 1 | 3 |
| `composables/useStudentIndexQuery.ts` | 1 | 2 |
| `pages/programCurriculum` | 1 | 2 |
| `pages/search` | 1 | 2 |
| `composables/useGlobalSearch.ts` | 1 | 1 |
| `pages/*.vue` (top level) | 1 | 1 |
| **Total** | **92** | **164** |

### 1.3 Route surface

Of 186 registered routes, **133 belong to `App\`**; the remainder are vendor-owned
(Laravel 23, Clockwork 10, Fruitcake 6, Livewire 5, Filament 4, Illuminate 2) plus 3 closures.
24 routes are unnamed — none of them are referenced from the frontend.

---

## 2. Target state and mapping rules

Wayfinder emits three trees under `resources/js/`:

- `wayfinder/` — the runtime core
- `actions/App/Http/Controllers/…` — one export per controller method, mirroring the PHP namespace
- `routes/…` — one export per **named** route, mirroring the dot-separated name

Since all 143 call sites reference route *names*, the `routes/` tree is the closest 1:1 mapping
and should be the default. The `actions/` tree is the right choice in the four cases in §3, where
the route name and the HTTP verb disagree.

> **Confirm the exact generated symbol shape in Phase 1** by running `wayfinder:generate` and
> reading the output before bulk-rewriting. The examples below assume the documented
> `import { name } from '@/routes/<segments>'` form; adjust the codemod if generation differs.

### 2.1 Mapping table

| # | Ziggy today | Wayfinder | Count |
|---|---|---|---|
| A | `route('students.index')` | `import { index } from '@/routes/students'` → `index.url()` | ~100 |
| B | `route('students.show', { student: s.slug })` | `show.url({ student: s.slug })` | ~30 |
| C | `form.post(route('x.store'))` | `form.submit(store())` — object carries the verb | 53 |
| D | `route('search', { q })` (query, not URI param) | `search.url({ query: { q } })` | 10 (§4) |
| E | `route().current('x')` | custom `isCurrent()` helper (§5) | 19 |

### 2.2 Worked examples

**A — plain URL in a breadcrumb** (`resources/js/pages/students/index/page.vue`):

```ts
// before
const breadcrumbs: BreadcrumbItem[] = [{ title: 'Students', href: route('students.index') }];

// after
import { index } from '@/routes/students';
const breadcrumbs: BreadcrumbItem[] = [{ title: 'Students', href: index.url() }];
```

`.url()` is required here, not optional: `NavItem.href` and `BreadcrumbItem.href` are typed
`string` in `resources/js/types/index.d.ts:40`, while a bare `index()` returns
`{ url, method }`. Either call `.url()` or widen the type — **prefer `.url()`**, it keeps the
type surface unchanged and avoids touching shared types.

**C — form submission** (`resources/js/pages/results/index/partials/resultForm.vue`):

```ts
// before
form.post(route('results.store'));

// after — Inertia v2 accepts the {url, method} object directly
import { store } from '@/routes/results';
form.submit(store());
```

This is the variant worth adopting deliberately rather than doing the minimal
`form.post(store.url())`: it is what makes §3's verb mismatches impossible to reintroduce.

**Link bindings** (33 sites) can drop `.url()` entirely — `<Link>` accepts the object:

```vue
<!-- before --> <Link :href="route('dashboard')">
<!-- after  --> <Link :href="dashboard()">
```

---

## 3. Pre-work — four defects this migration will surface

These exist **today** and are not caused by the migration, but Wayfinder turns three of them
from silent runtime behaviour into compile errors. Fix them **before** starting, as their own
commit, so the migration diff stays purely mechanical.

| # | Site | Problem |
|---|---|---|
| 3.1 | `resources/js/pages/auth/verifyEmail.vue:16`, `resources/js/pages/settings/Profile.vue:80` | `route('verification.send')` — **this route does not exist.** `routes/auth.php` registers no email-verification routes at all. Ziggy throws at runtime when either path is hit; Wayfinder will simply generate nothing to import. Either register the verification routes or delete the two dead references. |
| 3.2 | `resources/js/pages/auth/login.vue:22` | `form.post(route('login'))` — `login` is the name of the **GET** route (`routes/auth.php:19`); the POST route at line 22 is unnamed. It works only because both share the URI `/login`. |
| 3.3 | `resources/js/pages/auth/register.vue:16` | Same shape — `register` names the GET route (`routes/auth.php:14`); the POST is unnamed. |
| 3.4 | `resources/js/pages/auth/confirmPassword.vue:13` | Same shape — `password.confirm` names the GET route (`routes/auth.php:38`); the POST is unnamed. |

For 3.2–3.4, the clean fix is to import from `actions/` rather than `routes/`, which resolves to
the correct method:

```ts
import { store } from '@/actions/App/Http/Controllers/Auth/AuthenticatedSessionController';
form.submit(store());   // POST /login — correct by construction
```

Alternatively name the POST routes (`->name('login.store')` etc.). Either works; the `actions/`
route needs no backend change.

### 3.5 One closure route blocks generation

`routes/settings.php:20`:

```php
Route::get('settings/appearance', fn () => Inertia::render('settings/Appearance'))->name('appearance');
```

Wayfinder generates from controller actions and cannot represent a closure. `route('appearance')`
is used at `resources/js/layouts/settings/Layout.vue`. **Convert it to an invokable controller**
(`App\Http\Controllers\Settings\AppearanceController`) — which is the documented convention for
this codebase anyway (`CLAUDE.md`, "single-action invokable controllers are the norm"), so this
is a net improvement rather than migration tax.

The other two closures (`pulse`, and the unnamed `up` health check) are vendor/framework-owned and
never referenced from the frontend — ignore them.

---

## 4. Query-string call sites — the ten that need care

These routes have **no URI parameters**, so the object passed today is a query string. A naive
find-and-replace produces a silently wrong URL (Wayfinder would treat the object as route
parameters and drop them). Each must become `.url({ query: { … } })`.

| File | Line | Route | Passed |
|---|---|---|---|
| `resources/js/pages/search/page.vue` | 20 | `search` | `{ q }` via `router.get` |
| `resources/js/components/search/CommandPalette.vue` | 80 | `search` | `{ q }` |
| `resources/js/components/search/CommandPalette.vue` | 199 | `search` | `{ q }` |
| `resources/js/composables/useGlobalSearch.ts` | 48 | `search.suggestions` | `{ q }` |
| `resources/js/components/sidebar/Navigation.vue` | 26, 31, 36 | `download.{students,registrations,results}.page` | `{ selectedIndex: 0 }` |
| `resources/js/pages/export/results/page.vue` | 22 | `export.results.page` | `{ selectedIndex: 1 }` |
| `resources/js/pages/export/results/tabs/registrationNumbers.vue` | 19 | `export.results.registration-numbers.download` | `{ registration_numbers }` |
| `resources/js/pages/export/results/tabs/registrationNumber.vue` | 14 | `export.results.registration-number.download` | `{ registration_number }` |
| `resources/js/composables/useStudentIndexQuery.ts` | 36 | `students.index` | search/filter/sort params via `router.get` |
| `resources/js/composables/useStudentIndexQuery.ts` | 50 | `students.index` | search/filter/sort params in a sort link |

Note that `export.results.department-session.download`
(`resources/js/pages/export/results/tabs/departmentEntrySession.vue:23`) is **not** in this list —
its URI is `export/results/department/{department}/session/{session}`, so `{ department, session }`
really are route parameters and map straight across. Getting this distinction right is the one
place in the migration where care beats speed.

The two `students.index` sites are the highest-traffic of the ten: `useStudentIndexQuery` builds
the entire student-list URL — search term, four filters and the column sort — out of a params
object, so losing the query string there silently disables every control on that page.

**Verification for this section:** after conversion, exercise global search, the student list's
search/filters/sort, and each of the three export download tabs in a browser, confirming the
resulting URL still carries its query string.

---

## 5. Replacing `route().current()`

All 19 calls live in `resources/js/components/sidebar/Navigation.vue` and feed `NavItem.isActive`.
Wayfinder generates URLs; it has no concept of "the current route", so this needs a small helper.

```ts
// resources/js/composables/useCurrentUrl.ts
import { usePage } from '@inertiajs/vue3';

/** True when the given Wayfinder URL matches the page currently rendered. */
export function isCurrent(href: string): boolean {
    const page = usePage();
    const target = new URL(href, window.location.origin).pathname;
    return new URL(page.url, window.location.origin).pathname === target;
}
```

Usage:

```ts
import { page as studentsPage } from '@/routes/download/students';
import { isCurrent } from '@/composables/useCurrentUrl';

{ title: 'Students', href: studentsPage.url({ query: { selectedIndex: 0 } }), isActive: isCurrent(studentsPage.url()) }
```

Two behavioural notes:

- **Query strings must be excluded from the comparison.** Three of these nav items carry
  `?selectedIndex=0`; comparing full URLs would break their active state the moment a user
  switches tabs. Compare `pathname` only, as above.
- **Evaluation timing is unchanged.** `mainNav` is built once at `setup()`. No page in
  `resources/js/pages` declares a persistent layout, so the sidebar remounts on every Inertia
  visit and setup-time evaluation stays correct — matching today's Ziggy behaviour exactly.
  If persistent layouts are ever adopted, `mainNav` must become a `computed()`. Out of scope here;
  noted so the constraint is not lost.

`route().current()` also supports wildcards (`route().current('students.*')`). **No call site uses
this** — all 19 pass exact names — so the simple helper above is sufficient.

---

## 6. Tooling and CI changes

### 6.1 Generated files must not be linted or committed

`npm run check-build` runs `prettier --check "resources/**"`, `eslint "resources/**"` and
`vue-tsc` across everything under `resources/`. Wayfinder's output will fail Prettier and ESLint.

Add to `.gitignore`:

```
/resources/js/actions
/resources/js/routes
/resources/js/wayfinder
```

Add the same three paths to `.prettierignore`, and to the `ignores` array in `eslint.config.js`.
They must remain visible to `vue-tsc` — do **not** exclude them in `tsconfig.json:19`.

### 6.2 The `frontend` CI job needs a real Laravel install

Today it gets away with a shortcut — `composer require tightenco/ziggy` alone, no full install,
because Ziggy's JS ships in its own package directory. Wayfinder generation requires a **bootable
application**: full `composer install`, a `.env`, and an app key.

```yaml
# .github/workflows/ci.yml — frontend job, replacing "Install Ziggy"
- name: Install Composer Dependencies
  run: composer install -q --no-ansi --no-interaction --no-progress --prefer-dist

- name: Copy .env
  run: php -r "file_exists('.env') || copy('.env.example', '.env');"

- name: Generate key
  run: php artisan key:generate

- name: Generate Wayfinder Definitions
  run: php artisan wayfinder:generate
```

`docs/plans/improvements.md:387` already flags this job's `composer require` as a smell. This
migration makes the dependency heavier, not lighter — worth stating plainly rather than
discovering in a red build. Add the same `actions/cache@v4` composer cache the other jobs use so
the added install does not lengthen the job much.

Note the `frontend` job also pins PHP 8.4 while `composer.json` now targets PHP 8.5 — a full
`composer install` may surface that mismatch. Bump it in the same commit.

### 6.3 Vite

```js
import { wayfinder } from '@laravel/vite-plugin-wayfinder';
// plugins: [ …, wayfinder({ formVariants: true }) ]
```

Remove the `ziggy-js` alias from `vite.config.js:38` and `tsconfig.json:16` only in the final
teardown commit.

---

## 7. Phased execution

Wayfinder and Ziggy **coexist without conflict** — the global `route()` helper and generated
imports are independent. Migrate directory by directory with a green build at every commit.

| Phase | Work | Files | Est. |
|---|---|---|---|
| **0** | Pre-work fixes (§3): resolve `verification.send`, convert the `appearance` closure to an invokable controller | 3–4 | 1 h |
| **1** | Install `laravel/wayfinder` + `@laravel/vite-plugin-wayfinder`; wire the Vite plugin; run `wayfinder:generate`; **read the generated output and confirm the import shape assumed in §2**; update `.gitignore` / `.prettierignore` / `eslint.config.js` | ~6 | 1 h |
| **2** | CI: rework the `frontend` job (§6.2). Land before any bulk rewrite so later phases are actually gated | 1 | 30 m |
| **3** | Build `useCurrentUrl.ts` and migrate `Navigation.vue` (38 calls incl. all 19 `current()`) | 2 | 1.5 h |
| **4** | Migrate the ten query-string sites (§4) deliberately, by hand | 7 | 1 h |
| **5** | Bulk migrate: `pages/download` (19 files), `pages/students` (20) | 39 | 2.5 h |
| **6** | Bulk migrate the remainder: `pages/{auth,reports,finalResults,export,results,summary,vetting,graduands,settings,search,programCurriculum}`, `layouts/*`, `components/*`, `composables/*` | ~45 | 2.5 h |
| **7** | Teardown: `composer remove tightenco/ziggy`; delete `@routes`, `ZiggyVue`, `global.d.ts` declarations, both aliases; full manual sweep | 6 | 1 h |

**Total ≈ 11 hours.** Phases 5 and 6 are the ones that benefit from a codemod — a script that
maps `route('a.b.c', …)` to the right import plus call, run per directory with the build checked
after each. Do **not** run a codemod across Phases 3 and 4; those need judgement.

Phase 7 must not merge until a `grep -rn "route(" resources/js` returns zero hits.

---

## 8. Risk register

| Risk | Severity | Mitigation |
|---|---|---|
| Wayfinder is `v0.1.x`; a future release breaks 143 call sites | Medium | Pin exactly (`"laravel/wayfinder": "0.1.21"`) rather than a caret range; revisit at 1.0 |
| Query-string sites silently lose their params (§4) | **High** | Handled as its own hand-written phase; browser-verify all 8 |
| Sidebar active state regresses | Medium | Isolated to Phase 3; click every nav item before merging |
| Generated files break `check-build` | Medium | Phase 1 ignore-file changes; Phase 2 lands CI before bulk work |
| Vendor routes (53) pollute the generated tree | Low | Cosmetic — generated dirs are gitignored. Check whether the installed version filters vendor routes; if not, accept it |
| Type errors surface late, en masse | Low | `vue-tsc` after every phase, not just at the end |
| Merge conflicts with in-flight work | Medium | Land after `task/upgrade` merges; the unmerged search feature (`components/search/`, `composables/useGlobalSearch.ts`) touches 4 of the §4 sites |

---

## 9. Verification checklist

Automated (must pass at every phase boundary):

- [ ] `php artisan wayfinder:generate` succeeds
- [ ] `npm run check-build` — Prettier, ESLint, `vue-tsc`, Vite build
- [ ] `vendor/bin/pint --test`, `vendor/bin/phpcs`, `vendor/bin/phpstan --memory-limit=1G`
- [ ] `php artisan test --exclude-group=external --parallel` (Phase 0 touches routes, so the
      Feature tests under `tests/Feature/Controllers` are the real gate there)
- [ ] `grep -rn "route(" resources/js` returns nothing (Phase 7 only)

Manual — the PHP test suite cannot see any of this, and browser tests are gitignored:

- [ ] Every sidebar item navigates and highlights correctly (19 active states)
- [ ] Global search: palette (`Cmd-K`), suggestions endpoint, full search page — query strings intact
- [ ] Student list: search box, all four filters, all three sortable columns, and pagination while
      filtered — every one of these is a query string that must survive
- [ ] All three export download tabs produce a file, with params in the URL
- [ ] The three tabbed download pages open on the correct tab via `?selectedIndex`
- [ ] Login, register, password reset, confirm-password (the §3 verb fixes)
- [ ] Student profile: several of the 16 `student.*.update` PATCH forms
- [ ] Excel imports (results, final results, curriculum) still POST correctly
- [ ] Print/PDF links: `results.print`, `summary.print`, `composite.print`, `finalResults.transcript`

---

## 10. Rollback

Nothing here is destructive or data-affecting. Any phase reverts cleanly as a commit. After
Phase 7, rollback means restoring `tightenco/ziggy` in `composer.json`, the `@routes` directive,
the `ZiggyVue` registration and the two aliases — so keep Phase 7 as a single, self-contained,
revertable commit. The Phase 0 fixes are worth keeping regardless of whether the migration lands.

---

## Appendix A — the 96 route names used by the frontend

Grouped by first segment, with the number of distinct names in each:

`download` 20 · `student` 16 · `import` 9 · `export` 7 · `password` 6 · `finalResults` 5 ·
`vettingEvent` 4 · `students` 4 · `summary` 3 · `results` 3 · `profile` 3 · `composite` 3 ·
`search` 2 · `graduand` 2 · `department` 2 · `vetting` 1 · `verification` 1 (**unregistered**,
§3.1) · `register` 1 · `logout` 1 · `login` 1 · `dashboard` 1 · `appearance` 1 (**closure**, §3.5)

## Appendix B — highest-density files

| File | `route(` calls |
|---|---|
| `resources/js/components/sidebar/Navigation.vue` | 30 lines / 38 calls |
| `resources/js/pages/finalResults/index/page.vue` | 5 |
| `resources/js/pages/summary/view/page.vue` | 4 |
| `resources/js/pages/reports/composite/view/page.vue` | 4 |
| `resources/js/pages/settings/Profile.vue` | 3 |
| `resources/js/pages/results/index/page.vue` | 3 |
| `resources/js/layouts/settings/Layout.vue` | 3 |

The remaining 85 files carry one or two calls each.
