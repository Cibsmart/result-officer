<?php

declare(strict_types=1);

namespace App\Actions\Departments;

use App\Enums\ImportEventStatus;
use App\Enums\RawDataStatus;
use App\Models\ImportEvent;
use Exception;

final class ProcessImportedRawDepartments
{
    public function __construct(public SaveProcessedDepartment $saveDepartmentAction)
    {
    }

    public function execute(ImportEvent $event): void
    {
        $event->updateStatus(ImportEventStatus::PROCESSING);

        $rawDepartments = $event->departments()->where('status', RawDataStatus::PENDING)->get();

        foreach ($rawDepartments as $rawDepartment) {
            try {
                $this->saveDepartmentAction->execute($rawDepartment);
            } catch (Exception $e) {
                $rawDepartment->setMessage($e->getMessage());

                $rawDepartment->updateStatus(RawDataStatus::FAILED);
            }
        }

        $event->updateStatus(ImportEventStatus::COMPLETED);
    }
}
