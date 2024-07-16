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
        public readonly int $id,
        public readonly string $matriculationNumber,
        public readonly string $lastName,
        public readonly string $firstName,
        public readonly ?string $otherNames,
        public readonly string $name,
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
            id: $student->id,
            matriculationNumber: $student->matriculation_number,
            lastName: $student->last_name,
            firstName: $student->first_name,
            otherNames: $student->other_names,
            name: "$student->last_name $student->first_name $student->other_names",
            gender: $student->gender,
            birthDate: $student->date_of_birth,
            program: $student->program->name,
            department: $student->program->department->name,
            faculty: $student->program->department->faculty->name,
            admissionYear: $student->entrySession->name,
        );
    }
}
