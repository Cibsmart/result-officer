<?php

namespace App\Data\Results;

use App\Models\Enrollment;
use App\Models\Semester;
use App\Queries\InSemester;
use Illuminate\Support\Collection;
use Spatie\LaravelData\Data;

class SemesterResultData extends Data
{
    public function __construct(
        /** @var Collection<int, \App\Data\Results\ResultData> */
        public readonly Collection $results,
        public readonly string $semester,
        public int $totalCreditUnit,
        public int $totalGradePoint,
        public float $gradePointAverage
    ) {
    }

    public static function fromModel(Semester $semester, Enrollment $enrollment): self
    {
        $resultData = ResultData::collect($enrollment->results()
            ->tap(new InSemester($semester))
            ->get());

        $totalCreditUnit = $resultData->sum('creditUnit');

        $totalGradePoint = $resultData->sum('gradePoint');

        $gradePointAverage = round($totalGradePoint / $totalCreditUnit, 3);

        return new self(
            $resultData,
            $semester->name,
            $totalCreditUnit,
            $totalGradePoint,
            $gradePointAverage
        );
    }
}
