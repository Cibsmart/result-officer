<?php

declare(strict_types=1);

namespace App\Data\Results;

use App\Helpers\ComputeAverage;
use App\Models\SessionEnrollment;
use Illuminate\Support\Collection;
use Spatie\LaravelData\Data;

final class SessionResultData extends Data
{
    public function __construct(
        public readonly int $id,
        /** @var \Illuminate\Support\Collection<int, \App\Data\Results\SemesterResultData> */
        public readonly Collection $semesterResults,
        public readonly string $session,
        public readonly string $year,
        public readonly float $cumulativeGradePointAverage,
        public readonly string $formattedCGPA,
        public readonly int $resultsCount,
    ) {
    }

    public static function fromModel(SessionEnrollment $sessionEnrollment): self
    {
        $semesterEnrollments = SemesterResultData::collect(
            $sessionEnrollment->semesterEnrollments()->with('semester')->get(),
        );

        $cumulativeGradePointAverage = ComputeAverage::new(
            $semesterEnrollments->sum('gradePointAverage'),
            $semesterEnrollments->count(),
        )->value();

        $formattedCGPA = number_format($cumulativeGradePointAverage, 3);

        return new self(
            id: $sessionEnrollment->id,
            semesterResults: $semesterEnrollments,
            session: $sessionEnrollment->session->name,
            year: $sessionEnrollment->year->name,
            cumulativeGradePointAverage: $cumulativeGradePointAverage,
            formattedCGPA: $formattedCGPA,
            resultsCount: $semesterEnrollments->sum('resultsCount'),
        );
    }
}
