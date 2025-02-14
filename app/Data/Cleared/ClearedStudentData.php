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
        public readonly string $slug,
        public readonly string $batch,
        public readonly int $numberOfResults,
    ) {
    }

    public static function fromModel(Student $student): self
    {
        $gender = $student->gender;
        assert($gender instanceof Gender);

        $status = $student->status;
        assert($status instanceof StudentStatus);

        $finalStudent = $student->finalStudent;

        return new self(
            id: $student->id,
            name: $student->name,
            registrationNumber: $student->registration_number,
            gender: $gender,
            status: $status,
            fcgpa: number_format($finalStudent->final_cumulative_grade_point_average, 2),
            slug: $student->slug,
            batch: "{$finalStudent->month} {$finalStudent->year}",
            numberOfResults: $finalStudent->result_count,
        );
    }
}
