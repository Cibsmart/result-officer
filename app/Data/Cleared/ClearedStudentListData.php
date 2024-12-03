<?php

declare(strict_types=1);

namespace App\Data\Cleared;

use App\Data\Department\DepartmentData;
use App\Data\Faculty\FacultyData;
use App\Enums\StudentStatus;
use App\Models\Department;
use App\Models\Faculty;
use App\Models\StatusChangeEvent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Spatie\LaravelData\Data;

final class ClearedStudentListData extends Data
{
    public function __construct(
        public readonly FacultyData $faculty,
        public readonly DepartmentData $department,
        /** @var \Illuminate\Support\Collection<int, \App\Data\Cleared\ClearedStudentData> */
        public readonly Collection $data,
    ) {
    }

    public static function fromModel(Department $department, int $year): self
    {
        $clearedStudents = $department->students()
            ->whereHas('statusChangeEvents', function (Builder $query) use ($year): void {
                $query->where('status', StudentStatus::CLEARED)
                    ->whereYear('date', $year);
            })
            ->where('students.status', 'cleared')
            ->orderByDesc(StatusChangeEvent::query()->select('date')
                ->where('status', StudentStatus::CLEARED)
                ->whereColumn('students.id', 'student_id')
                ->latest()
                ->take(1),
            )
            ->get();

        $faculty = $department->faculty;
        assert($faculty instanceof Faculty);

        return new self(
            faculty: FacultyData::fromModel($faculty),
            department: DepartmentData::fromModel($department),
            data: ClearedStudentData::collect($clearedStudents),
        );
    }
}
