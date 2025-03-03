<?php

declare(strict_types=1);

namespace App\Data\Students;

use App\Enums\StatusColor;
use App\Enums\StudentStatus;
use App\Models\Student;
use Spatie\LaravelData\Data;

final class StudentBasicInfoData extends Data
{
    public function __construct(
        public readonly int $id,
        public readonly string $registrationNumber,
        public readonly string $name,
        public readonly string $departmentProgram,
        public readonly string $slug,
        public readonly StudentStatus $status,
        public readonly StatusColor $statusColor,
    ) {
    }

    public static function fromModel(Student $student): self
    {
        $status = $student->status;

        $program = $student->program;
        $department = $program->department;

        $departmentProgram = $department->name === $program->name
            ? $program->name
            : "{$department->name} ({$program->name})";

        return new self(
            id: $student->id,
            registrationNumber: $student->registration_number,
            name: "$student->name",
            departmentProgram: $departmentProgram,
            slug: $student->slug,
            status: $status,
            statusColor: $status->color(),
        );
    }
}
