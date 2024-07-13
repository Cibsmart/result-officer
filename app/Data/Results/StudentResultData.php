<?php

declare(strict_types=1);

namespace App\Data\Results;

use App\Models\Student;
use App\Services\ComputeAverage;
use App\Services\DegreeClass;
use Illuminate\Support\Collection;
use Spatie\LaravelData\Data;

final class StudentResultData extends Data
{
    public function __construct(
        public readonly int $id,
        /** @var \Illuminate\Support\Collection<int, \App\Data\Results\SessionResultData> */
        public readonly Collection $enrollments,
        public readonly float $finalCumulativeGradePointAverage,
        public readonly string $classOfDegree,
    ) {
    }

    public static function fromModel(Student $student): self
    {
        $enrollments = SessionResultData::collect($student->enrollments);

        $finalCGPA = round(
            ComputeAverage::new(
                $enrollments->sum('cumulativeGradePointAverage'),
                $enrollments->count(),
            )->value(),
            2,
        );

        $degreeClass = DegreeClass::for($finalCGPA)->value();

        return new self($student->id, $enrollments, $finalCGPA, $degreeClass->value);
    }
}
