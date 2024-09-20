<?php

declare(strict_types=1);

namespace App\Console\Commands\ImportPortalData;

use App\Contracts\PortalDataService;
use App\Enums\ImportEventStatus;
use App\Models\ImportEvent;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Context;

final class ImportPortalData extends Command
{
    protected $signature = 'portal-data:import {eventId}';

    protected $description = 'Import Data from the Portal and Save in the Raw Data Table in the Database';

    /**
     * @template T of \App\Contracts\PortalDataService
     * @param T $service
     */
    public function handle(PortalDataService $service): int
    {
        $event = ImportEvent::findOrFail($this->argument('eventId'));

        if ($event->status !== ImportEventStatus::STARTED) {
            $event->updateStatus(ImportEventStatus::FAILED);

            return Command::FAILURE;
        }

        $event->updateStatus(ImportEventStatus::DOWNLOADING);

        try {
            $courses = $service->get([]);
        } catch (Exception $e) {
            $event->message = $e->getMessage();
            $event->updateStatus(ImportEventStatus::FAILED);

            return Command::FAILURE;
        }

        $event->updateStatus(ImportEventStatus::DOWNLOADED);

        $event->updateDownloadCount($courses->count());

        $event->updateStatus(ImportEventStatus::SAVING);

        $service->save($event, $courses);

        $event->updateStatus(ImportEventStatus::SAVED);

        Context::add('import-event', $event->type->value);
        Artisan::call('portal-data:process', ['eventId' => $event->id]);

        return Command::SUCCESS;
    }
}
