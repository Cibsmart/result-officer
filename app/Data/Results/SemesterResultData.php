<?php

declare(strict_types=1);

namespace App\Data\Results;

use App\Models\SemesterEnrollment;
use Illuminate\Support\Collection;
use Spatie\LaravelData\Data;

final class SemesterResultData extends Data
{
    public function __construct(
        /** @var \Illuminate\Support\Collection<int, \App\Data\Results\ResultData> */
        public readonly Collection $results,
        public readonly string $semester,
        public int $totalCreditUnit,
        public int $totalGradePoint,
        public float $gradePointAverage,
    ) {
    }

    public static function fromModel(SemesterEnrollment $enrollment): self
    {
        $courses = ResultData::collect($enrollment->courses);

        $totalCreditUnit = $courses->sum('creditUnit');

        $totalGradePoint = $courses->sum('gradePoint');

        dump($courses, $enrollment, $totalGradePoint, $totalCreditUnit);
        $gradePointAverage = round($totalGradePoint / $totalCreditUnit, 3);

        return new self($courses, $enrollment->semester->name, $totalCreditUnit, $totalGradePoint, $gradePointAverage);
    }
}
