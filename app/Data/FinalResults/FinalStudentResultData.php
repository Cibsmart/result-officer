<?php

declare(strict_types=1);

namespace App\Data\FinalResults;

use App\Enums\ClassOfDegree;
use App\Models\Student;
use App\Values\SessionValue;
use Illuminate\Support\Collection;
use Spatie\LaravelData\Data;

final class FinalStudentResultData extends Data
{
    public function __construct(
        public readonly int $id,
        /** @var \Illuminate\Support\Collection<int, \App\Data\FinalResults\FinalSessionResultData> */
        public readonly Collection $finalSessionEnrollments,
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
        $finalSessionEnrollments = FinalSessionResultData::collect(
            $student->sessionEnrollments()->with(['finalSemesterEnrollments', 'session'])->orderBy('session_id')->get(),
        );

        $finalStudent = $student->finalStudent()->first();

        $finalCGPA = $finalStudent->final_cumulative_grade_point_average;

        $degreeClass = ClassOfDegree::for($finalCGPA);
        $programType = $student->program->programType;
        $lastSession = $finalSessionEnrollments->last();
        $graduationYear = $lastSession
            ? SessionValue::new($lastSession->session)->lastYear()
            : 0;

        return new self(
            id: $student->id,
            finalSessionEnrollments: $finalSessionEnrollments,
            finalCumulativeGradePointAverage: $finalCGPA,
            formattedFCGPA: number_format($finalCGPA, 2),
            degreeClass: $degreeClass->value,
            degreeAwarded: "$programType->name ($programType->code)",
            graduationYear: $graduationYear,
            resultsCount: $finalStudent->result_count,
        );
    }
}
