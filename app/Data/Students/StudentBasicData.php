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
        public readonly string $photoUrl,
    ) {
    }

    public static function fromModel(Student $student): self
    {
        $birthDate = DateValue::fromValue($student->date_of_birth)->toString();

        $status = $student->status;
        assert($status instanceof StudentStatus);

        $gender = $student->gender;
        assert($gender instanceof Gender);

        return new self(
            id: $student->id,
            registrationNumber: $student->registration_number,
            lastName: $student->last_name,
            firstName: $student->first_name,
            otherNames: $student->other_names,
            name: "$student->last_name $student->first_name $student->other_names",
            gender: $gender,
            birthDate: $birthDate,
            program: $student->program->name,
            department: $student->program->department->name,
            faculty: $student->program->department->faculty->name,
            admissionYear: $student->entrySession->firstYear(),
            nationality: $student->lga->state->country->demonym,
            slug: $student->slug,
            status: $status,
            statusColor: $status->color(),
            photoUrl: $student->photo_url ? $student->photo_url : '',
        );
    }
}
