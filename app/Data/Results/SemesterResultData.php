<?php

declare(strict_types=1);

namespace App\Data\Results;

use App\Models\SemesterEnrollment;
use App\Services\ComputeAverage;
use Illuminate\Support\Collection;
use Spatie\LaravelData\Data;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
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
    ) {
    }

    public static function fromModel(SemesterEnrollment $enrollment): self
    {

        $courses = ResultData::collect($enrollment->courses)
            ->sortBy('courseCode')
            ->values();

        $totalCreditUnit = (int) $courses->sum('creditUnit');
        $totalGradePoint = (int) $courses->sum('gradePoint');

        $gpa = ComputeAverage::new($totalGradePoint, $totalCreditUnit)->value();

        return new self(
            id: $enrollment->id,
            results: $courses,
            semester: $enrollment->semester->name,
            creditUnitTotal: $totalCreditUnit,
            gradePointTotal: $totalGradePoint,
            gradePointAverage: $gpa);
    }
}
