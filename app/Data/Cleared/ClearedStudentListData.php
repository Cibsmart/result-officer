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

    public static function fromModel(Department $department, int $year, string $month): self
    {
        $clearedStudents = $department->students()->with('finalStudent')
            ->whereHas('finalStudent', function (Builder $query) use ($year, $month): void {
                $query->where('year', $year)->where('month', $month);
            })
            ->whereIn('status', [StudentStatus::CLEARED, StudentStatus::GRADUATED])
            ->orderByDesc('registration_number')
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
