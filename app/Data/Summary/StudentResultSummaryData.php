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
        public readonly float $fcgpa,
    ) {
    }

    public static function fromModel(Student $student): self
    {
        return new self(
            student: StudentBasicData::fromModel($student),
            fcgpa: StudentResultData::from($student)->finalCumulativeGradePointAverage,
        );
    }
}
