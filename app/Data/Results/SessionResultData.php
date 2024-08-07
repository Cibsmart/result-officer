<?php

declare(strict_types=1);

namespace App\Data\Results;

use App\Models\Enrollment;
use App\Services\ComputeAverage;
use Illuminate\Support\Collection;
use Spatie\LaravelData\Data;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
final class SessionResultData extends Data
{
    public function __construct(
        public readonly int $id,
        /** @var \Illuminate\Support\Collection<int, \App\Data\Results\SemesterResultData> */
        public readonly Collection $semesterResults,
        public readonly string $session,
        public readonly string $year,
        public readonly float $cumulativeGradePointAverage,
    ) {
    }

    public static function fromModel(Enrollment $enrollment): self
    {
        $semesters = SemesterResultData::collect($enrollment->semesters)
            ->sortBy('semester')
            ->values();

        $cumulativeGradePointAverage = ComputeAverage::new(
            $semesters->sum('gradePointAverage'),
            $semesters->count(),
        )->value();

        return new self(
            id: $enrollment->id,
            semesterResults: $semesters,
            session: $enrollment->session->name,
            year: $enrollment->year->name,
            cumulativeGradePointAverage: $cumulativeGradePointAverage);
    }
}
