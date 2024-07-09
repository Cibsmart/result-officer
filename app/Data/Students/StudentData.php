<?php

declare(strict_types=1);

namespace App\Data\Students;

use App\Enums\GenderEnum;
use App\Models\Student;
use Carbon\Carbon;
use Spatie\LaravelData\Data;

final class StudentData extends Data
{
    public function __construct(
        public readonly string $matriculationNumber,
        public readonly string $name,
        public readonly string $lastName,
        public readonly string $firstName,
        public readonly ?string $otherNames,
        public readonly GenderEnum $gender,
        public readonly Carbon $birthDate,
        public readonly string $program,
        public readonly string $department,
        public readonly string $faculty,
        public readonly string $admissionYear,
    ) {
    }

    public static function fromModel(Student $student): self
    {
        return new self(
            $student->matriculation_number,
            $student->name,
            $student->last_name,
            $student->first_name,
            $student->other_names,
            GenderEnum::from($student->gender),
            $student->date_of_birth,
            $student->program->name,
            $student->program->department->name,
            $student->program->department->faculty->name,
            $student->entrySession->name,
        );
    }
}
