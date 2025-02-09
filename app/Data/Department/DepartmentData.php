<?php

declare(strict_types=1);

namespace App\Data\Department;

use App\Data\Program\ProgramListData;
use App\Models\Department;
use Spatie\LaravelData\Data;

final class DepartmentData extends Data
{
    public function __construct(
        public readonly int $id,
        public readonly string $name,
        public readonly string $slug,
        public readonly ?ProgramListData $programs,
    ) {
    }

    public static function fromModel(Department $department): self
    {
        $programs = ProgramListData::forDepartment($department);

        return new self(id: $department->id, name: $department->name, slug: $department->slug, programs: $programs);
    }
}
