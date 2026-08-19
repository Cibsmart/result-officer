# Review: the `Final*` record family

A critique of the parallel `Student → SessionEnrollment → SemesterEnrollment → Registration → Result`
and `FinalStudent → FinalSessionEnrollment → FinalSemesterEnrollment → FinalResult` model families,
and options for replacing the arrangement.

---

## 1. It is not actually a replication

The framing "the same five models, duplicated" understates the problem. The two families differ in
four ways, and every one of them blocks code sharing.

**1. The archive collapses a level.** `Registration` + `Result` (two tables) become `FinalResult`
(one). `final_results` carries `course_status` and `credit_unit` from the registration alongside
`scores`, `total_score`, `grade` and `grade_point` from the result. So the shapes are not
interchangeable — no query, DTO or renderer can be written once and pointed at either.

**2. The archive has aggregates; the live side has none.** Every `final_*` table carries
`result_count`, `credit_unit_sum`, `grade_point_sum` and a GPA/CGPA column, materialised by
`updateCountSumAndAverages()`. The live side stores none of this and recomputes it on every read in
`SemesterResultData` / `SessionResultData` / `StudentResultData`. Two storage models, and therefore
two computation paths (§3).

**3. The snapshot is partial.** `FinalCourse` freezes course code and title — good instinct. But
`final_session_enrollments.session_id` and `.level_id`, `final_results.lecturer_id`, and the student's
own name, registration number and program all remain live foreign keys. So renaming a program,
correcting a level name or editing a lecturer record **retroactively rewrites every historical
transcript**. The archive is immutable in exactly one dimension out of six.

**4. It is not only an archive.** `final_*` has two independent write paths:

| Path | Source | Live records? |
|------|--------|---------------|
| `ClearStudent::execute` | Snapshot of live records at clearance | Yes |
| `ProcessRawFinalResults::execute` | Direct Excel import of historical graduates | Often none — `final_results.registration_id` is nullable |

So `final_*` is simultaneously a derived archive *and* a primary store for students who never had
live records. That dual role is the origin of most of the awkwardness: it cannot be treated as
disposable and rebuildable, because for some students it is the only copy.

---

## 2. What the design gets right

The goal is correct and worth preserving in any alternative.

- **A graduated student's transcript must not change.** Editing a course title, recomputing a result
  or fixing a curriculum must not alter a degree awarded in 2019. Point-in-time snapshotting is the
  right instinct, and most systems that skip it regret it.
- **Precomputing aggregates for graduates is sensible.** A transcript for a cleared student is read
  far more often than it is written, and the numbers can never legitimately change.
- **`FinalCourse` is the right idea**, just applied to one dimension instead of all of them.

The problem is not the intent. It is that the execution achieves neither immutability nor economy.

---

## 3. Exhibit A: the two families compute different numbers

The clearest cost of maintaining two of everything is that the two implementations have drifted, and
the drift is in the most consequential number the system produces.

### 3.1 Degree class can change at clearance — and can become FAIL

`ClassOfDegree::for()` matches against closed ranges with **gaps between them**:

```
FIRST_CLASS          4.50 – 5.00
SECOND_CLASS_UPPER   3.50 – 4.49     ← gap: 4.49 < x < 4.50
SECOND_CLASS_LOWER   2.50 – 3.49     ← gap: 3.49 < x < 3.50
THIRD_CLASS          1.50 – 2.49     ← gap
PASS                 1.00 – 1.49     ← gap
FAIL                 0.00 – 0.99     ← gap
default → FAIL
```

A value inside a gap matches no case and falls through to `default => self::FAIL`.

- **Live path** — `StudentResultData::fromModel` computes
  `round(ComputeAverage::new(...)->value(), 2)`. Two decimals can never land in a gap. Safe.
- **Final path** — `FinalStudent::getFinalCumulativeGradePointAverage()` rounds to **three**
  decimals, and `FinalStudentResultData` passes that straight to `ClassOfDegree::for()`.

So a student whose FCGPA is 4.495 is shown **SECOND CLASS UPPER** before clearance and
**FAIL** after it. The same holds at 3.495, 2.495, 1.495 and 0.995. This is not theoretical; it is
reachable by any FCGPA whose third decimal places it in a 0.01-wide band, at five different
boundaries.

Worth counting on production data before anything else in this document:

```sql
SELECT id, student_id, final_cumulative_grade_point_average / 1000 AS fcgpa
FROM final_students
WHERE (final_cumulative_grade_point_average % 1000) > 490
  AND (final_cumulative_grade_point_average % 1000) < 500
  AND FLOOR(final_cumulative_grade_point_average / 1000) BETWEEN 0 AND 4;
```

The immediate fix is independent of any restructuring: make the ranges contiguous (`>= min` and
`< nextMin`), or round to two decimals at the single point where a class is derived. **Do this
first, and separately.**

### 3.2 CGPA itself differs under the UNIVERSAL strategy

`FinalSessionEnrollment::getCumulativeGradePointAverage()` branches on
`Institution::first()->strategy`:

```php
strategy === SEMESTER ? avg(semester GPAs) : gradePointSum / creditUnitSum
```

`SessionResultData::fromModel` — the live equivalent — has no branch. It always computes
`avg(semester GPAs)`. Under `ComputationStrategy::UNIVERSAL` the two paths return different session
CGPAs for identical underlying results, and therefore different final CGPAs and potentially different
degree classes.

Either the live path is missing the strategy, or the final path shouldn't have it. Only one of them
can be right, and nothing in the codebase forces them to agree.

### 3.3 Division-by-zero guards exist on one side only

`ComputeAverage::value()` returns `0.000` when the divisor is zero. The live path uses it everywhere.
The final path does its own arithmetic and does not:

- `FinalSessionEnrollment::getCumulativeGradePointAverage()` — under `UNIVERSAL`, computes
  `getGradePointSum() / getCreditUnitSum()` with **no zero guard at all**. A session whose results
  carry zero credit units raises `DivisionByZeroError`.
- `FinalStudent::getFinalCumulativeGradePointAverage()` guards with
  `count() === 0 && getCreditUnitSum() === 0` — an **`&&` where `||` was meant**. With enrollments
  present but zero total credit units, it falls through to the same unguarded division.

A shared helper already exists and solves this. The final path simply does not call it — which is
what happens when the same logic is written twice.

### 3.4 The averaging is an average of averages

Under `SEMESTER`: semester GPA = ΣGP/ΣCU, session CGPA = mean(semester GPAs), final CGPA =
mean(session CGPAs). Three unweighted levels of averaging, which diverges from the credit-weighted
CGPA whenever semesters carry unequal credit loads — precisely the case for carryover and final-year
students.

This may well be deliberate institutional policy; the `ComputationStrategy` enum suggests it is. But
it is worth confirming explicitly and documenting, because it is invisible in the code and produces
numbers that a credit-weighted calculation would not.

---

## 4. Structural defects

Independent of the computation drift:

| Defect | Detail |
|--------|--------|
| Redundant keys | `final_session_enrollments` carries both `student_id` and `final_student_id`, with `unique(student_id, session_id)` — a uniqueness constraint expressed against the wrong parent. |
| Archive depends on live | `final_students.student_id` is a constrained FK. The archive cannot survive deletion of the live student, and is not detachable for export. |
| No soft deletes | Live tables use `SoftDeletes`; `final_*` does not. An erroneously created archive row can only be hard-deleted. |
| Cached model instances | `FinalSessionEnrollment::getOrCreate` and `FinalSemesterEnrollment::getOrCreate` wrap `firstOrCreate` in `Cache::remember(..., 5 minutes)`, serialising Eloquent models into the cache during import. A re-run inside the window gets a stale instance; a deleted row is served from cache. |
| `Institution::first()` in loops | Called per session and per student inside the aggregate methods, once per row. |
| Duplicated import validation | `Pipelines/Checks/ExcelImports/RawFinalResults/` (14 classes) largely restates `RawExcelResults/` (10 classes). |
| Duplicated read stack | `app/Data/FinalResults/` (5 classes) mirrors `app/Data/Results/` (5 classes) with different internals. |

### Scale of the duplication

Roughly 5 models (616 lines), 5 Data classes, 14 pipeline checks, a controller, a ViewModel and a
transcript renderer exist solely because the archive has a different shape from the source. Add the
three `Legacy*` models and this family is a substantial fraction of the ~55-model surface. Every new
result-facing feature must be built twice, and the sections above show what happens when it is built
twice by different hands at different times.

---

## 5. Alternatives

### Option A — One family, frozen on finalisation

Delete the `Final*` tables. Keep one set of records for every student, ever. At clearance:

1. copy the dimension values that must not change onto the rows themselves as literals
   (`course_code`, `course_title`, `program_name`, `level_name`, `session_name`, `student_name`,
   `registration_number`);
2. materialise the aggregates into columns that already exist for everyone (`credit_unit_sum`,
   `grade_point_sum`, `grade_point_average`, …), computed by the one shared code path;
3. stamp `finalised_at` and make the rows write-protected from then on, enforced by a model observer
   rather than by convention.

**Gains** — one read stack, one computation path, one set of import checks. The drift in §3 becomes
structurally impossible. ~25 classes deleted.
**Costs** — write-protection must be genuinely enforced. Live queries must filter finalised rows
(largely already true via `StudentStatus`). Historical graduates imported from Excel need live rows
synthesised, which is a real migration but arguably the correct record anyway: those students *did*
register for those courses.

### Option B — One family plus an immutable transcript document

Option A, plus: at clearance, render the complete transcript into an `academic_transcripts` row as a
fully-resolved JSON document — every name, code, title and number a literal, no foreign keys — with a
content hash, and a handful of extracted columns (`final_cgpa`, `class_of_degree`, `graduation_year`,
`exam_officer`) for querying.

**Gains** — everything in A, plus *true* immutability where it legally matters. The document is what
you produce and verify years later, and it is provably unchanged. The relational rows remain the
working copy; the document is the artefact. `scores` is already stored as JSON, so this is not a new
idea in this codebase.
**Costs** — one more table, and a rule about which of the two is authoritative when they disagree
(the document, always).

### Option C — Keep two families, but fix the archive

Freeze *all* dimensions as literals, drop the FKs from `final_*` to live tables, route both families
through one shared computation service, and share the read stack behind a common interface.

**Gains** — least disruptive; preserves the current mental model; fixes §3 and most of §4.
**Costs** — keeps the duplication permanently. Still two of everything to build and keep in step,
which is the condition that produced §3 in the first place.

### Option D — Archive as a rebuildable projection

Snapshot document as the source of truth (as in B), plus a flat, fully-denormalised
`graduate_results` table for reporting and analytics, rebuildable from the documents.

**Gains** — immutability plus queryability across graduates.
**Costs** — two representations, though one is derived and regenerable, which is a far weaker coupling
than two hand-maintained families.

---

## 6. Recommendation

**Option B.** One live family, finalisation freezes literals and materialises aggregates, plus an
immutable transcript document as the legal artefact.

It is the only option that achieves what the current design was reaching for — a transcript that
genuinely cannot change — while removing the duplication that has already produced a degree-class bug.
Option A alone leaves the "provably unchanged" requirement unmet; Option C leaves the duplication that
caused the drift; Option D is B plus a projection you can add later if reporting demands it.

### Sequence

| Step | Work | Why here |
|------|------|----------|
| **0** | Fix `ClassOfDegree` range gaps; unify rounding at the point of classification. Audit affected `final_students`. | Independent, urgent, hours of work. Do not wait for a restructuring. |
| **1** | Extract one `ResultAggregator` service. Point both families at it. Resolve the §3.2 strategy question explicitly. | Stops the bleeding, removes drift, is a prerequisite for merging anyway. |
| **2** | Add literal-freezing columns and `finalised_at` to the live tables. Backfill nothing yet. | Additive, reversible. |
| **3** | Write finalisation into the live family. Dual-write to `final_*` for a period. Diff continuously. | The parity phase; same discipline as the vetting cutover. |
| **4** | Add `academic_transcripts` documents. Render from live-family finalised rows. | The artefact. |
| **5** | Migrate historical graduates: synthesise live rows from `final_*` and `Legacy*`, generate documents. | The largest and riskiest step; do it after 3 has proved the shape correct. |
| **6** | Move readers to the unified stack. Delete `app/Data/FinalResults`, the duplicated checks, the five models. Archive and drop `final_*`. | Only once nothing reads them. |

Step 5 is where this can go wrong. Export `final_*` and `legacy_*` before touching them: for a
population of graduates they are the only surviving record, and a degree awarded on their basis may
need defending years from now.

---

## 7. Interaction with the vetting redesign

The two projects meet at `ClearStudent`, and helpfully rather than awkwardly.

- `Student::canBeCleared()` is the gate in both documents. Settle it once: vetting decides
  *whether* the student may be finalised; finalisation decides *what is frozen*.
- The vetting run's `record_digest` (`vetting-redesign.md` §6.3) is exactly the proof that records did
  not change between the passing verdict and finalisation. Under Option B, finalisation should assert
  the digest still matches and record it on the transcript document — the clearance then carries its
  own evidence.
- `ClearStudent` currently performs a large multi-table write with **no transaction**. Whichever
  option is chosen, wrap it.
- Do **not** run these two migrations concurrently. Vetting Phase 0–3 touches reconciliation and
  reads; this touches the archive and writes. Finish vetting through its parity phase first.
