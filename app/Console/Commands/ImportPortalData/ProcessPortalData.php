<?php

declare(strict_types=1);

namespace App\Console\Commands\ImportPortalData;

use App\Contracts\PortalDataService;
use App\Enums\ImportEventStatus;
use App\Models\ImportEvent;
use Illuminate\Console\Command;

final class ProcessPortalData extends Command
{
    protected $signature = 'portal-data:process {eventId}';

    protected $description = 'Process all Pending Raw Data associated with the event';

    /**
     * @template T of \App\Contracts\PortalDataService
     * @param T $service
     */
    public function handle(PortalDataService $service): void
    {
        $event = ImportEvent::findOrFail($this->argument('eventId'));

        if (in_array($event->status, ImportEventStatus::unprocessableStates(), true)) {
            return;
        }

        $event->updateStatus(ImportEventStatus::PROCESSING);

        $service->process($event);

        $event->updateStatus(ImportEventStatus::COMPLETED);
    }
}
