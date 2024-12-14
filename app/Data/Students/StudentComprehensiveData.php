<?php

declare(strict_types=1);

namespace App\Data\Students;

use App\Models\Student;
use Spatie\LaravelData\Data;

final class StudentComprehensiveData extends Data
{
    public function __construct(
        public readonly StudentBasicData $basic,
    ) {
    }

    public static function fromModel(Student $student): self
    {
        return new self(basic: StudentBasicData::fromModel($student));
    }
}
