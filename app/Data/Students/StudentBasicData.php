<?php

declare(strict_types=1);

namespace App\Data\Students;

use App\Enums\Gender;
use App\Enums\StatusColor;
use App\Enums\StudentStatus;
use App\Models\Student;
use Spatie\LaravelData\Data;

final class StudentBasicData extends Data
{
    public function __construct(
        public readonly int $id,
        public readonly string $registrationNumber,
        public readonly string $lastName,
        public readonly string $firstName,
        public readonly ?string $otherNames,
        public readonly string $name,
        public readonly Gender $gender,
        public readonly string $birthDate,
        public readonly string $program,
        public readonly string $department,
        public readonly string $faculty,
        public readonly int $admissionYear,
        public readonly string $nationality,
        public readonly string $slug,
        public readonly StudentStatus $status,
        public readonly StatusColor $statusColor,
    ) {
    }

    public static function fromModel(Student $student): self
    {
        $birthDate = $student->date_of_birth
            ? $student->date_of_birth->format('d/m/Y')
            : '';

        $status = $student->status;
        assert($status instanceof StudentStatus);

        return new self(
            id: $student->id,
            registrationNumber: $student->registration_number,
            lastName: $student->last_name,
            firstName: $student->first_name,
            otherNames: $student->other_names,
            name: "$student->last_name $student->first_name $student->other_names",
            gender: $student->gender,
            birthDate: $birthDate,
            program: $student->program->name,
            department: $student->program->department->name,
            faculty: $student->program->department->faculty->name,
            admissionYear: $student->entrySession->firstYear(),
            nationality: $student->government->state->country->demonym,
            slug: $student->slug,
            status: $status,
            statusColor: $status->color(),
        );
    }
}
