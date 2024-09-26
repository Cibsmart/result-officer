<?php

declare(strict_types=1);

namespace App\Actions\Registrations;

use App\Data\Download\PortalRegistrationData;
use App\Enums\RawDataStatus;
use App\Models\ImportEvent;
use App\Models\RawRegistration;

final class SavePortalRegistration
{
    public function execute(ImportEvent $event, PortalRegistrationData $data): void
    {
        $exists = RawRegistration::query()
            ->where('online_id', $data->onlineId)
            ->where('status', RawDataStatus::PROCESSED)
            ->exists();

        if ($exists) {
            return;
        }

        RawRegistration::createFromPortalRegistrationData($data, $event);
    }
}
