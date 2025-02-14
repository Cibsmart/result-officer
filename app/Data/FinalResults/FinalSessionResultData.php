<?php

declare(strict_types=1);

namespace App\Data\FinalResults;

use App\Models\FinalSessionEnrollment;
use Illuminate\Support\Collection;
use Spatie\LaravelData\Data;

final class FinalSessionResultData extends Data
{
    public function __construct(
        public readonly int $id,
        /** @var \Illuminate\Support\Collection<int, \App\Data\FinalResults\FinalSemesterResultData> */
        public readonly Collection $semesterResults,
        public readonly string $session,
        public readonly string $year,
        public readonly float $cumulativeGradePointAverage,
        public readonly string $formattedCGPA,
        public readonly int $resultsCount,
    ) {
    }

    public static function fromModel(FinalSessionEnrollment $finalSessionEnrollment): self
    {
        $finalSemesterEnrollments = FinalSemesterResultData::collect(
            $finalSessionEnrollment->finalSemesterEnrollments()->with('semester')->get(),
        );

        $cumulativeGradePointAverage = $finalSessionEnrollment->cummulative_grade_point_average;

        $formattedCGPA = number_format($cumulativeGradePointAverage, 3);

        return new self(
            id: $finalSessionEnrollment->id,
            semesterResults: $finalSemesterEnrollments,
            session: $finalSessionEnrollment->session->name,
            year: $finalSessionEnrollment->year->name,
            cumulativeGradePointAverage: $cumulativeGradePointAverage,
            formattedCGPA: $formattedCGPA,
            resultsCount: $finalSessionEnrollment->result_count,
        );
    }
}
