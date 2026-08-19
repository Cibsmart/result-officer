# Implementation Roadmap

Single ordering across all six plan documents in this directory. Each plan sequences itself
internally; this one resolves the order *between* them, and records the three places where they
contradict each other.

| Plan | Internal phases | Bulk effort |
|---|---|---|
| `improvements.md` | 12 sequenced items, P0–P5 | mixed |
| `audit-trail.md` | 0–8 | ~1.5 weeks |
| `filament-migration.md` | 0–5 | 4–6 weeks |
| `vetting-redesign.md` | 0–7 | large |
| `final-records-review.md` | 0–6 | large |
| `ziggy-to-wayfinder-migration.md` | 0–7 | ~11 hours |

Waves are ordered. Within a wave, items are independent unless noted.

---

## Wave A — Land the branch

**A1.** Merge `task/upgrade` into `main`.

It already carries a framework upgrade, the openspout swap, the mPDF swap and the unmerged global
search feature. Every plan below adds to it. `ziggy-to-wayfinder-migration.md` §0 makes merging first
an explicit precondition; it is really a precondition for all of them.

---

## Wave B — Stop the bleeding (~2 days total)

Every item is independent, involves no design decision, and is actively corrupting or hiding data.
Nothing else in this document should start first.

| # | Work | Source |
|---|---|---|
| B1 | Close open public registration | `improvements.md` §2.4 |
| B2 | Fix the four migration `down()` methods that drop the wrong table | §1.1 |
| B3 | Fix `Role::creatable()` label swap, then audit existing `users.role` values | §1.2 |
| B4 | `ClearanceController` never rolls back its transaction | §1.3 |
| B5 | Excel import events strand in `PROCESSING` on validation failure | §1.4 |
| B6 | ~~`ResultUpdateAction` audits stale values~~ — **false positive**; dead line removed, regression test added | §1.6 (retracted) |
| B7 | Schedule `rp:process-queued-vetting` | §1.5 |
| B8 | `ClassOfDegree` range gaps + rounding at classification; audit affected `final_students` | `final-records-review.md` Step 0 |
| B9 | Rotate the Sentry DSN committed to `.env.example` | §2.5 |

B3 is a blocker for `filament-migration.md` Phase 3.

B6 turned out not to be a bug at all. The audit trail already records grade changes correctly, so
Wave F loses the prerequisite it was thought to have — and `audit-trail.md` loses its Phase 0.

B7 fixes a subsystem that Wave I deletes. Do it anyway: it is one line, it makes group vetting usable
in the meantime, and Wave I's parity phase needs the old system actually running to diff against.

---

## Wave C — Make CI trustworthy (~1 day)

Everything after this is gated by CI, so CI has to mean something first.

- **C1** — Pin CI dependencies; resolve the committed-`yarn.lock`-but-npm-in-CI split (§5.1, §5.2).
- **C2** — Move PHPStan null suppressions into the baseline (§5.4).
- **C3** — Resolve PHP version drift between CI and `composer.json` (§5.3).

---

## Wave D — Authorization (~1.5 weeks)

**The single biggest unblocker in the backlog.** It gates `filament-migration.md` Phases 1–4,
`audit-trail.md` §7.4, and `vetting-redesign.md` §10. Doing it once, here, is much cheaper than three
partial versions.

- **D1** — Deny-by-default authorization on the HTTP layer + negative feature tests (§2.1, §5.5).
  This *is* `filament-migration.md` Phase 0.2; do them as one piece of work.
- **D2** — Department scoping on download/export requests — one shared trait covers ~20 endpoints (§2.3).
- **D3** — Decide whether the four inert roles get real capabilities (§2.2). Cheaper to answer while
  writing policies than to retrofit (`filament-migration.md` open decision 3).
- **D4** — `routes/admin.php`, free the `/admin` path, policy scaffold, negative-auth test helper
  (`filament-migration.md` Phase 0.3, 0.4).

---

## Wave E — Wayfinder (~1.5 days)

Run `ziggy-to-wayfinder-migration.md` Phases 0–7 in full, as its own branch.

**Why this early, ahead of much higher-priority work:** Wave G writes 4–6 weeks of new Inertia admin
pages. Every one of them will contain `route()` calls. Migrating now costs ~11 hours; migrating after
Wave G means writing those pages twice. The plan's own risk register flags merge conflicts with
in-flight frontend work as Medium — that risk only grows.

It is also the one plan with no dependency on any other, so it can run in parallel with Wave D if a
second person is available.

---

## Wave F — Audit trail (~1.5 weeks)

`audit-trail.md` Phases 1–8 (there is no Phase 0; see B6), plus one item pulled forward from `improvements.md`:

- **F0** — Wrap Action write paths in transactions (§3.3, item 9). Pulled here because an audit row
  written outside the transaction of the mutation it describes can outlive a rollback. B4 fixed the
  worst instance; this generalises it. Prerequisite for the rest of the wave, not a parallel task.

Placed before Wave G so the new admin controllers are audited as they are written, rather than
retrofitted. See the correction in "Contradictions" below.

---

## Wave G — Filament → Inertia (4–6 weeks)

`filament-migration.md` Phases 1–5. Phase 0 was absorbed into Waves B3 and D.

**Additional requirement not in that plan:** every new admin controller calls `RecordAudit` as it is
built. Curriculum edits in particular require a remark and memo per `audit-trail.md` decision 2,
which settles that plan's open decision 4.

**Decision point at the Phase 3 boundary.** The plan names stopping after Phase 3 as a defensible
outcome, keeping Filament for the curriculum drill-down alone. Take that decision there, with the
real cost of Phases 1–3 known — but see the vetting contradiction below, which changes what stopping
early actually costs.

---

## Wave H — Async architecture (~1.5 weeks)

- **H1** — Convert vetting and Excel processing to queued jobs with `$tries`/`backoff`; the
  `everyMinute` commands become thin dispatchers (§3.1, §3.2, item 11).
- **H2** — Portal API client: timeouts, retry, remove the production-unsafe assertion (§3.4).

Placed here because `vetting-redesign.md` assumes queued execution on Horizon as a starting
condition — its Phase 4 cannot be built on the current one-record-per-minute scheduler. H1 is the
only item in the backlog that `improvements.md` flags as wanting a design discussion first.

---

## Wave I — Vetting redesign (large)

`vetting-redesign.md` Phases 0–7. Depends on Wave D (policies), Wave F (waiver audit records),
Wave H (queued execution), and — pending the decision below — Wave G's admin pattern.

Phase 3 (parity) is the phase that earns the rewrite's safety and the one most likely to be dropped
under time pressure. It is also the gate for Wave J.

---

## Wave J — Final records (large)

`final-records-review.md` Steps 1–6. Step 0 already landed in Wave B8.

Starts **after `vetting-redesign.md` Phase 3**, per that plan's §7: vetting decides *whether* a
student may be finalised, finalisation decides *what is frozen*, and they meet at `ClearStudent`.

The one permitted overlap: Steps 1–2 (extract `ResultAggregator`, add freezing columns) are additive
and may run alongside vetting Phases 4–7. Step 3 onward must not — that is where writes to the
archive begin.

Step 5 (migrating historical graduates) is the riskiest single step in the entire backlog. Export
`final_*` and `legacy_*` before touching them; for a population of graduates they are the only
surviving record.

---

## Wave K — Performance and hygiene

- **K1** — Index coverage audit (§4.2).
- **K2** — SQLite-in-tests vs MySQL-in-production divergence (§4.3).
- **K3** — Structure and hygiene (§6).

**Void:** §4.1 (N+1 queries inside vetting steps). Wave I deletes those classes. The new engine's
snapshot loader (`vetting-redesign.md` §4.1) addresses the same problem by design. Do not spend time
here.

---

## Contradictions between the plans

### 1. Filament is deleted, but vetting assumes it — **needs a decision**

`filament-migration.md` §0 ends with `composer remove filament/filament` and `app/Filament/` deleted.
`vetting-redesign.md` assumes "**Filament 3** hosts rule-set administration" and its Phase 5 builds a
`VettingRuleSetResource`.

These cannot both happen. Three ways out:

| Option | Consequence |
|---|---|
| **Build rule-set admin in Inertia** (recommended) | Vetting Phase 5 depends on Wave G's pattern being settled (Phase 2), not on all of Wave G. Consistent with why the Filament migration exists at all. |
| Build it in Filament, delete it later | Wasted work, and it lands in the exact bypass the migration is removing. |
| Stop Filament migration after Phase 3, keep Filament for curriculum + rule sets | Cheapest short-term; permanently keeps two admin stacks and two write paths. |

**Recommendation: Inertia.** The Filament migration exists because Filament resources write straight
to Eloquent, bypassing `app/Actions/` and the audit trail. A rule-set editor changes *who can
graduate* — it is the last thing that should sit on an unaudited write path. This overturns an
assumption in `vetting-redesign.md`, so it needs your call rather than mine.

### 2. `audit-trail.md` open question 4 — resolved, and my earlier answer was wrong

That plan recommends adding audit coverage to the Filament admin resources as the immediate
follow-up. That is wasted work: Wave G deletes those resources. The coverage belongs in the *new*
Inertia admin controllers, written in as they are built (Wave G above). I have corrected the plan.

### 3. `improvements.md` §4.1 and §1.5 both target code slated for deletion

§1.5 is worth doing anyway (one line, and Wave I's parity phase needs the old system running).
§4.1 is not — see Wave K.

---

## Critical path

```
A1 → B (2d) → C (1d) → D (1.5w) → F (1.5w) → G (4-6w) → H (1.5w) → I (large) → J (large)
                         └→ E (1.5d, parallel-able)
```

Waves A–D are roughly **three weeks** and close every confirmed correctness bug plus the whole
authorization gap. That is the point at which the system stops actively degrading; everything after
is structural.
