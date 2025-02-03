<?php

declare(strict_types=1);

namespace App\Data\Results;

use App\Enums\ClassOfDegree;
use App\Helpers\ComputeAverage;
use App\Models\Student;
use App\Values\SessionValue;
use Illuminate\Support\Collection;
use Spatie\LaravelData\Data;

final class StudentResultData extends Data
{
    public function __construct(
        public readonly int $id,
        /** @var \Illuminate\Support\Collection<int, \App\Data\Results\SessionResultData> */
        public readonly Collection $sessionEnrollments,
        public readonly float $finalCumulativeGradePointAverage,
        public readonly string $formattedFCGPA,
        public readonly string $degreeClass,
        public readonly string $degreeAwarded,
        public readonly int $graduationYear,
        public readonly int $resultsCount,
    ) {
    }

    public static function fromModel(Student $student): self
    {
        $sessionEnrollments = SessionResultData::collect(
            $student->sessionEnrollments()->with(['semesterEnrollments', 'session'])->orderBy('session_id')->get(),
        );

        $finalCGPA = round(
            ComputeAverage::new(
                $sessionEnrollments->sum('cumulativeGradePointAverage'),
                $sessionEnrollments->count(),
            )->value(),
            2,
        );

        $degreeClass = ClassOfDegree::for($finalCGPA);
        $programType = $student->program->programType;
        $lastSession = $sessionEnrollments->last();
        $graduationYear = $lastSession
            ? SessionValue::new($lastSession->session)->lastYear()
            : 0;

        return new self(
            id: $student->id,
            sessionEnrollments: $sessionEnrollments,
            finalCumulativeGradePointAverage: $finalCGPA,
            formattedFCGPA: number_format($finalCGPA, 2),
            degreeClass: $degreeClass->value,
            degreeAwarded: "$programType->name ($programType->code)",
            graduationYear: $graduationYear,
            resultsCount: $sessionEnrollments->sum('resultsCount'),
        );
    }
}
