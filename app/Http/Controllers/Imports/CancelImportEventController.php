<?php

declare(strict_types=1);

namespace App\Http\Controllers\Imports;

use App\Enums\ImportEventStatus;
use App\Models\ImportEvent;

final class CancelImportEventController
{
    public function __invoke(ImportEvent $event): void
    {
        $event->updateStatus(ImportEventStatus::CANCELLED);
    }
}
