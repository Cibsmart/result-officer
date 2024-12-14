<?php

declare(strict_types=1);

namespace App\Data\Students;

use App\Data\Results\StudentResultData;
use App\Models\Student;
use Spatie\LaravelData\Data;

final class StudentComprehensiveData extends Data
{
    public function __construct(
        public readonly StudentData $student,
        public readonly StudentResultData $results,
    ) {
    }

    public static function fromModel(Student $student): self
    {
        return new self(
            student: StudentData::fromModel($student),
            results: StudentResultData::fromModel($student),
        );
    }
}
