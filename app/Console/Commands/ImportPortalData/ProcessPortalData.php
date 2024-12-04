<?php

declare(strict_types=1);

namespace App\Console\Commands\ImportPortalData;

use App\Enums\ImportEventStatus;
use App\Models\ImportEvent;
use App\Services\Api\PortalServiceFactory;
use Illuminate\Console\Command;

final class ProcessPortalData extends Command
{
    protected $signature = 'rp:process-portal-data {eventId}';

    protected $description = 'Process all Pending Raw Data associated with the event';

    public function handle(PortalServiceFactory $factory): int
    {
        $event = ImportEvent::findOrFail($this->argument('eventId'));

        $service = $factory->resolve($event->type);

        if (in_array($event->status, ImportEventStatus::unprocessableStates(), true)) {
            return Command::FAILURE;
        }

        $event->updateStatus(ImportEventStatus::PROCESSING);

        $service->process($event);

        $event->updateStatus(ImportEventStatus::COMPLETED);

        return Command::SUCCESS;
    }
}
