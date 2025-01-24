<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Enums\ImportEventStatus;
use App\Models\ImportEvent;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

final class ProcessQueuedImportEvent extends Command
{
    protected $signature = 'rp:process-queued-import';

    protected $description = 'Checks for Queued Import Events and Initiates Processing';

    public function __invoke(): int
    {
        $event = ImportEvent::query()
            ->where('status', ImportEventStatus::QUEUED)
            ->orderBy('id')
            ->first();

        if ($event) {
            $event->updateStatus(ImportEventStatus::STARTED);

            Artisan::call('rp:import-group-portal-data', ['eventId' => $event->id]);
        }

        return Command::SUCCESS;
    }
}
