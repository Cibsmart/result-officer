<?php

declare(strict_types=1);

namespace App\Data\Students;

use App\Models\Student;
use Spatie\LaravelData\Data;

final class StudentData extends Data
{
    public function __construct(
        public readonly int $id,
        public readonly StudentBasicData $basic,
        public readonly StudentOtherData $others,
    ) {
    }

    public static function fromModel(Student $student): self
    {
        return new self(
            id: $student->id,
            basic: StudentBasicData::fromModel($student),
            others: StudentOtherData::fromModel($student),
        );
    }
}
