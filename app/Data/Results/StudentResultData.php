<?php

declare(strict_types=1);

namespace App\Data\Results;

use App\Models\Student;
use App\Services\ComputeAverage;
use App\Services\DegreeClass;
use App\Services\RetrieveYear;
use Illuminate\Support\Collection;
use Spatie\LaravelData\Data;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
final class StudentResultData extends Data
{
    public function __construct(
        public readonly int $id,
        /** @var \Illuminate\Support\Collection<int, \App\Data\Results\SessionResultData> */
        public readonly Collection $enrollments,
        public readonly float $finalCumulativeGradePointAverage,
        public readonly string $degreeClass,
        public readonly string $degreeAwarded,
        public readonly int $graduationYear,
    ) {
    }

    public static function fromModel(Student $student): self
    {
        $enrollments = SessionResultData::collect($student->enrollments)
            ->sortBy('session')
            ->values();

        $finalCGPA = round(
            ComputeAverage::new(
                $enrollments->sum('cumulativeGradePointAverage'),
                $enrollments->count(),
            )->value(),
            2,
        );

        $degreeClass = DegreeClass::for($finalCGPA)->value();
        $programType = $student->program->programType;
        $lastSession = $student->enrollments->last();
        $graduationYear = $lastSession
            ? RetrieveYear::fromSession($lastSession->session->name)->lastYear()
            : 0;

        return new self(
            id: $student->id,
            enrollments: $enrollments,
            finalCumulativeGradePointAverage: $finalCGPA,
            degreeClass: $degreeClass->value,
            degreeAwarded: "$programType->name ($programType->code)",
            graduationYear: $graduationYear,
        );
    }
}
