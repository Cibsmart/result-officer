<?php

declare(strict_types=1);

namespace App\Actions\Imports\Departments;

use App\Enums\RawDataStatus;
use App\Models\Department;
use App\Models\RawDepartment;

final class ProcessPortalDepartment
{
    public function execute(RawDepartment $rawDepartment): void
    {
        $exists = Department::query()
            ->where('code', $rawDepartment->code)
            ->where('name', $rawDepartment->name)
            ->exists();

        if ($exists) {
            $rawDepartment->updateStatus(RawDataStatus::DUPLICATE);

            return;
        }

        $department = Department::createFromRawDepartment($rawDepartment);

        $rawDepartment->updateStatusAndDepartment(RawDataStatus::PROCESSED, $department);
    }
}
