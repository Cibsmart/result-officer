<?php

declare(strict_types=1);

namespace App\Data\Summary;

use App\Data\Results\StudentResultData;
use App\Data\Students\StudentBasicData;
use App\Models\Student;
use Spatie\LaravelData\Data;

final class StudentResultSummaryData extends Data
{
    public function __construct(
        public readonly StudentBasicData $student,
        public readonly string $fcgpa,
        public readonly int $resultsCount,
    ) {
    }

    public static function fromModel(Student $student): self
    {
        $studentResultData = StudentResultData::from($student);

        return new self(
            student: StudentBasicData::fromModel($student),
            fcgpa: $studentResultData->formattedFCGPA,
            resultsCount: $studentResultData->resultsCount,
        );
    }
}
