<?php

declare(strict_types=1);

namespace App\Actions\Departments;

use App\Data\Download\PortalDepartmentData;
use App\Enums\RawDataStatus;
use App\Models\ImportEvent;
use App\Models\RawDepartment;

final class SaveRawDepartment
{
    public function execute(ImportEvent $event, PortalDepartmentData $data): void
    {
        $exists = RawDepartment::query()
            ->where('code', $data->departmentCode)
            ->where('name', $data->departmentName)
            ->where('faculty', $data->facultyName)
            ->where('status', RawDataStatus::PROCESSED)
            ->exists();

        if ($exists) {
            return;
        }

        RawDepartment::createFromPortalDepartmentData($data, $event);

    }
}
