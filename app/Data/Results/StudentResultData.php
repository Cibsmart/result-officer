<?php

namespace App\Data\Results;

use App\Models\Student;
use Illuminate\Support\Collection;
use Spatie\LaravelData\Data;

final class StudentResultData extends Data
{
    public function __construct(
        /** @var Collection<int, \App\Data\Results\SessionResultData> */
        public readonly Collection $enrollments,
        public readonly float $finalCumulativeGradePointAverage
    ) {
    }

    public static function fromModel(Student $student): StudentResultData
    {
        $enrollments = SessionResultData::collect(
            $student->enrollments()->whereHas('results')->get()
        );

        $finalCGPA = round(
            $enrollments->sum('cummulativeGradePointAverage') / $enrollments->count(),
            2
        );

        return new self(
            $enrollments,
            $finalCGPA
        );
    }
}
