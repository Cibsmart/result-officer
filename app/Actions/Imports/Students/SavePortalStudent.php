<?php

declare(strict_types=1);

namespace App\Actions\Import\Students;

use App\Data\Download\PortalStudentData;
use App\Enums\RawDataStatus;
use App\Models\ImportEvent;
use App\Models\RawStudent;

final class SavePortalStudent
{
    public function execute(ImportEvent $event, PortalStudentData $data): void
    {
        $exists = RawStudent::query()
            ->where('registration_number', $data->registrationNumber)
            ->where('status', RawDataStatus::PROCESSED)
            ->exists();

        if ($exists) {
            return;
        }

        RawStudent::createFromPortalStudentData($data, $event);
    }
}
