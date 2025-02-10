<?php

declare(strict_types=1);

namespace App\Actions\Imports\Results;

use App\Data\Download\PortalResultData;
use App\Enums\RawDataStatus;
use App\Models\ImportEvent;
use App\Models\RawResult;

final class SavePortalResult
{
    public function execute(ImportEvent $event, PortalResultData $data): void
    {
        $exists = RawResult::query()
            ->where('online_id', $data->onlineId)
            ->where('status', RawDataStatus::PROCESSED)
            ->exists();

        if ($exists) {
            return;
        }

        RawResult::createFromPortalResultData($data, $event);
    }
}
