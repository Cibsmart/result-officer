<?php

declare(strict_types=1);

namespace App\Data\Department;

use App\Data\Faculty\FacultyData;
use App\Models\Department;
use App\Models\Faculty;
use Spatie\LaravelData\Data;

final class DepartmentInfoData extends Data
{
    public function __construct(
        public readonly FacultyData $faculty,
        public readonly DepartmentData $department,
    ) {
    }

    public static function for(Department $department): self
    {
        $faculty = $department->faculty;
        assert($faculty instanceof Faculty);

        return new self(
            faculty: FacultyData::fromModel($faculty),
            department: DepartmentData::fromModel($department),
        );
    }
}
