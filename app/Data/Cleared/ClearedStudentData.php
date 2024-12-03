<?php

declare(strict_types=1);

namespace App\Data\Cleared;

use App\Enums\Gender;
use App\Enums\StudentStatus;
use App\Models\Student;
use Spatie\LaravelData\Data;

final class ClearedStudentData extends Data
{
    public function __construct(
        public readonly int $id,
        public readonly string $name,
        public readonly string $registrationNumber,
        public readonly Gender $gender,
        public StudentStatus $status,
        public readonly string $fcgpa,
        public readonly string $dateCleared,
    ) {
    }

    public static function fromModel(Student $student): self
    {
        $clearEvent = $student->statusChangeEvents()
            ->where('status', StudentStatus::CLEARED)
            ->first();

        return new self(
            id: $student->id,
            name: $student->name,
            registrationNumber: $student->registration_number,
            gender: $student->gender,
            status: $student->status,
            fcgpa: number_format($student->fcgpa, 2),
            dateCleared: $clearEvent->date->format('jS M, Y'),
        );
    }
}
