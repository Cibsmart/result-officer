<?php

declare(strict_types=1);

namespace App\Data\Students;

use App\Models\Student;
use Illuminate\Contracts\Pagination\Paginator;
use Spatie\LaravelData\Data;

final class StudentListPaginatedData extends Data
{
    public function __construct(
        public readonly Paginator $students,
    ) {
    }

    public static function new(): self
    {
        $students = Student::query()
            ->with('program.department.faculty', 'entrySession', 'government.state.country')
            ->paginate();

        $data = StudentData::collect($students);

        return new self(students: $data);
    }
}
