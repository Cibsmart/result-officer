<?php

declare(strict_types=1);

namespace App\Data\Results;

use App\Models\Enrollment;
use App\Services\ComputeAverage;
use Illuminate\Support\Collection;
use Spatie\LaravelData\Data;

final class SessionResultData extends Data
{
    public function __construct(
        public readonly int $id,
        /** @var \Illuminate\Support\Collection<int, \App\Data\Results\SemesterResultData> */
        public readonly Collection $semesterResults,
        public readonly string $session,
        public readonly float $cummulativeGradePointAverage,
    ) {
    }

    public static function fromModel(Enrollment $enrollment): self
    {
        $semesters = SemesterResultData::collect($enrollment->semesters);

        $cumulativeGradePointAverage = ComputeAverage::new(
            (int) $semesters->sum('gradePointAverage'),
            $semesters->count()
        )->value();

        return new self($enrollment->id, $semesters, $enrollment->session->name, $cumulativeGradePointAverage);
    }
}
