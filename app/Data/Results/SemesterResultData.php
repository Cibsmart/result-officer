<?php

declare(strict_types=1);

namespace App\Data\Results;

use App\Helpers\ComputeAverage;
use App\Models\SemesterEnrollment;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Spatie\LaravelData\Data;

final class SemesterResultData extends Data
{
    public function __construct(
        public readonly int $id,
        /** @var \Illuminate\Support\Collection<int, \App\Data\Results\ResultData> */
        public readonly Collection $results,
        public readonly string $semester,
        public readonly int $creditUnitTotal,
        public readonly int $gradePointTotal,
        public readonly float $gradePointAverage,
        public readonly string $formattedCreditUnitTotal,
        public readonly string $formattedGradePointTotal,
        public readonly string $formattedGPA,
    ) {
    }

    public static function fromModel(SemesterEnrollment $enrollment): self
    {

        $courses = ResultData::collect(
            $enrollment->courses()
                ->with(['course', 'result'])
                ->orderBy('course_id')
                ->get(),
        );

        $totalCreditUnit = (int) $courses->sum('creditUnit');
        $totalGradePoint = (int) $courses->sum('gradePoint');

        $gpa = ComputeAverage::new($totalGradePoint, $totalCreditUnit)->value();

        return new self(
            id: $enrollment->id,
            results: $courses,
            semester: $enrollment->semester->name,
            creditUnitTotal: $totalCreditUnit,
            gradePointTotal: $totalGradePoint,
            gradePointAverage: $gpa,
            formattedCreditUnitTotal: Str::of((string) $totalCreditUnit)->padLeft(2, '0')->value(),
            formattedGradePointTotal: Str::of((string) $totalGradePoint)->padLeft(2, '0')->value(),
            formattedGPA: number_format($gpa, 3),
        );
    }
}
