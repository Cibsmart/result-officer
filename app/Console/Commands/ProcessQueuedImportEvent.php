<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Enums\ImportEventMethod;
use App\Enums\ImportEventStatus;
use App\Enums\ImportEventType;
use App\Models\ImportEvent;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

final class ProcessQueuedImportEvent extends Command
{
    protected $signature = 'rp:process-queued-import';

    protected $description = 'Checks for Queued Import Events and Initiates Processing';

    public function __invoke(): int
    {
        $events = ImportEvent::query()
            ->where('status', ImportEventStatus::QUEUED)
            ->orderBy('id')
            ->get();

        foreach ($events as $event) {
            $event->updateStatus(ImportEventStatus::STARTED);

            if ($event->method === ImportEventMethod::DEPARTMENT_SESSION
                && in_array($event->type, ImportEventType::canBeGrouped(), true)) {

                Artisan::call('rp:import-group-portal-data', ['eventId' => $event->id]);

                continue;
            }

            Artisan::call('rp:import-portal-data', ['eventId' => $event->id]);
        }

        return Command::SUCCESS;
    }
}
