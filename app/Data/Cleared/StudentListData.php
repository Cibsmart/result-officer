<?php

declare(strict_types=1);

namespace App\Data\Cleared;

use App\Models\Department;
use Illuminate\Support\Collection;
use Spatie\LaravelData\Data;

final class StudentListData extends Data
{
    public function __construct(
        /** @var \Illuminate\Support\Collection<int, \App\Data\Cleared\StudentData> */
        public readonly Collection $data,
    ) {
    }

    public static function fromModel(Department $department, int $year): self
    {
        $students = $department->students()
            ->whereRelation('statusChangeEvents', 'status', 'cleared')
            ->where('students.status', 'cleared')
            ->get();

        return new self(data: StudentData::collect($students));
    }
}
