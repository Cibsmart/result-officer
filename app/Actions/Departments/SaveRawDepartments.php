<?php

declare(strict_types=1);

namespace App\Actions\Departments;

use App\Enums\ImportEventStatus;
use App\Models\ImportEvent;
use Illuminate\Support\Collection;

final class SaveRawDepartments
{
    public function __construct(public SaveRawDepartment $saveRawDepartmentAction)
    {
    }

    /** @param \Illuminate\Support\Collection<int, \App\Data\Download\PortalDepartmentData> $departments */
    public function execute(ImportEvent $event, Collection $departments): void
    {
        if ($event->status !== ImportEventStatus::DOWNLOADED) {
            $event->updateStatus(ImportEventStatus::FAILED);

            return;
        }

        $event->updateStatus(ImportEventStatus::SAVING);

        foreach ($departments as $department) {
            $this->saveRawDepartmentAction->execute($event, $department);
        }

        $event->updateStatus(ImportEventStatus::SAVED);
    }
}
