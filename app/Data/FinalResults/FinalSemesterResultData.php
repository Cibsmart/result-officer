<?php

declare(strict_types=1);

namespace App\Data\FinalResults;

use App\Models\FinalSemesterEnrollment;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Spatie\LaravelData\Data;

final class FinalSemesterResultData extends Data
{
    public function __construct(
        public readonly int $id,
        /** @var \Illuminate\Support\Collection<int, \App\Data\FinalResults\FinalResultData> */
        public readonly Collection $results,
        public readonly string $semester,
        public readonly int $creditUnitTotal,
        public readonly int $gradePointTotal,
        public readonly float $gradePointAverage,
        public readonly string $formattedCreditUnitTotal,
        public readonly string $formattedGradePointTotal,
        public readonly string $formattedGPA,
        public readonly int $resultsCount,
    ) {
    }

    public static function fromModel(FinalSemesterEnrollment $finalSemesterEnrollment): self
    {

        $finaResultData = FinalResultData::collect(
            $finalSemesterEnrollment->finalResults()->with(['finalCourse'])->orderBy('final_course_id')->get(),
        );

        $creditUnitTotal = $finalSemesterEnrollment->credit_unit_sum;
        $gradePointTotal = $finalSemesterEnrollment->grade_point_sum;
        $gradePointAverage = $finalSemesterEnrollment->grade_point_average;

        return new self(
            id: $finalSemesterEnrollment->id,
            results: $finaResultData,
            semester: $finalSemesterEnrollment->semester->name,
            creditUnitTotal: $creditUnitTotal,
            gradePointTotal: $gradePointTotal,
            gradePointAverage: $gradePointAverage,
            formattedCreditUnitTotal: Str::of((string) $creditUnitTotal)->padLeft(2, '0')->value(),
            formattedGradePointTotal: Str::of((string) $gradePointTotal)->padLeft(2, '0')->value(),
            formattedGPA: number_format($gradePointAverage, 3),
            resultsCount: $finalSemesterEnrollment->result_count,
        );
    }
}
