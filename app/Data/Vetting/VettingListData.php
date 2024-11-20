<?php

declare(strict_types=1);

namespace App\Data\Vetting;

use App\Data\Department\DepartmentData;
use App\Data\Faculty\FacultyData;
use App\Enums\StudentStatus;
use App\Models\Department;
use App\Models\Faculty;
use Illuminate\Support\Collection;
use Spatie\LaravelData\Data;

final class VettingListData extends Data
{
    public function __construct(
        public readonly FacultyData $faculty,
        public readonly DepartmentData $department,
        /** @var \Illuminate\Support\Collection<int, \App\Data\Vetting\VettingStudentData> */
        public readonly Collection $graduands,
    ) {
    }

    public static function fromModel(Department $department): self
    {
        $graduands = $department->students()
            ->with('vettingEvent')
            ->whereIn('status', [StudentStatus::FINAL_YEAR, StudentStatus::EXTRA_YEAR])
            ->get();

        $faculty = $department->faculty;
        assert($faculty instanceof Faculty);

        return new self(
            faculty: FacultyData::fromModel($faculty),
            department: DepartmentData::fromModel($department),
            graduands: VettingStudentData::collect($graduands),
        );
    }
}
