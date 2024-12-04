<?php

declare(strict_types=1);

namespace App\Console\Commands\ImportPortalData;

use App\Enums\ImportEventStatus;
use App\Models\ImportEvent;
use App\Services\Api\PortalServiceFactory;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

final class ImportPortalData extends Command
{
    protected $signature = 'rp:import-portal-data {eventId}';

    protected $description = 'Import Data from the Portal and Save in the Raw Data Table in the Database';

    public function handle(PortalServiceFactory $factory): int
    {
        $event = ImportEvent::findOrFail($this->argument('eventId'));

        $service = $factory->resolve($event->type);

        $event->updateStatus(ImportEventStatus::DOWNLOADING);

        try {
            $data = $service->get($event->method, $event->data);
        } catch (Exception $e) {
            $event->setMessage($e->getMessage());
            $event->updateStatus(ImportEventStatus::FAILED);

            return Command::FAILURE;
        }

        $event->updateStatus(ImportEventStatus::DOWNLOADED);

        $event->updateDownloadCount($data->count());

        $event->updateStatus(ImportEventStatus::SAVING);

        $service->save($event, $data);

        $event->updateStatus(ImportEventStatus::SAVED);

        Artisan::call('rp:process-portal-data', ['eventId' => $event->id]);

        return Command::SUCCESS;
    }
}
