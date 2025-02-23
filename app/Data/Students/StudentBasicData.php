<?php

declare(strict_types=1);

namespace App\Data\Students;

use App\Enums\Gender;
use App\Enums\StatusColor;
use App\Enums\StudentStatus;
use App\Models\Student;
use App\Values\DateValue;
use Spatie\LaravelData\Data;

final class StudentBasicData extends Data
{
    public function __construct(
        public readonly int $id,
        public readonly string $registrationNumber,
        public readonly string $lastName,
        public readonly string $firstName,
        public readonly string $otherNames,
        public readonly string $name,
        public readonly Gender $gender,
        public readonly string $birthDate,
        public readonly string $program,
        public readonly string $department,
        public readonly string $faculty,
        public readonly string $departmentProgram,
        public readonly int $admissionYear,
        public readonly string $nationality,
        public readonly string $slug,
        public readonly StudentStatus $status,
        public readonly StatusColor $statusColor,
        public readonly string $photoUrl,
        public readonly int $departmentId,
        public readonly int $programId,
        public readonly int $entrySessionId,
    ) {
    }

    public static function fromModel(Student $student): self
    {
        $status = $student->status;

        $gender = $student->gender;

        $otherNames = $student->other_names
            ? $student->other_names
            : '';

        $program = $student->program;
        $department = $program->department;

        $departmentProgram = $department->name === $program->name
            ? $program->name
            : "{$department->name} ({$program->name})";

        return new self(
            id: $student->id,
            registrationNumber: $student->registration_number,
            lastName: $student->last_name,
            firstName: $student->first_name,
            otherNames: $otherNames,
            name: "$student->name",
            gender: $gender,
            birthDate: DateValue::fromValue($student->date_of_birth)->toString(),
            program: $program->name,
            department: $department->name,
            faculty: $department->faculty->name,
            departmentProgram: $departmentProgram,
            admissionYear: $student->entrySession->firstYear(),
            nationality: $student->lga->state->country->demonym,
            slug: $student->slug,
            status: $status,
            statusColor: $status->color(),
            photoUrl: $student->photo_url ? $student->photo_url : '',
            departmentId: $department->id,
            programId: $program->id,
            entrySessionId: $student->entry_session_id,
        );
    }
}
