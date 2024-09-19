<?php

declare(strict_types=1);

namespace App\Console\Commands\Departments;

use App\Actions\Departments\SaveRawDepartments;
use App\Enums\ImportEventStatus;
use App\Models\ImportEvent;
use App\Services\Api\DepartmentService;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

final class ImportAndSaveDepartments extends Command
{
    protected $signature = 'departments:import {eventId}';

    protected $description = 'Import Courses from the Portal and Save in the Database';

    public function handle(DepartmentService $service, SaveRawDepartments $saveRawDepartmentsAction): void
    {
        $event = ImportEvent::findOrFail($this->argument('eventId'));

        $event->updateStatus(ImportEventStatus::DOWNLOADING);

        try {
            $courses = $service->getAllDepartments();
        } catch (Exception $e) {
            $event->message = $e->getMessage();
            $event->updateStatus(ImportEventStatus::FAILED);

            return;
        }

        $event->download_count = $courses->count();
        $event->save();

        $saveRawDepartmentsAction->execute($event, $courses);

        Artisan::call('departments:process', ['eventId' => $event->id]);
    }
}
