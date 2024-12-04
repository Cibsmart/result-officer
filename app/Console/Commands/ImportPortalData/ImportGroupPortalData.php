<?php

declare(strict_types=1);

namespace App\Console\Commands\ImportPortalData;

use App\Enums\ImportEventStatus;
use App\Enums\StudentStatus;
use App\Models\Department;
use App\Models\ImportEvent;
use App\Models\Session;
use App\Services\Api\PortalServiceFactory;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

final class ImportGroupPortalData extends Command
{
    protected $signature = 'rp:import-group-portal-data {eventId}';

    protected $description = 'Import Data from the Portal per Student and Save in the Raw Data Table in the Database';

    public function handle(PortalServiceFactory $factory): int
    {
        $event = ImportEvent::findOrFail($this->argument('eventId'));

        $service = $factory->resolve($event->type);

        $event->updateStatus(ImportEventStatus::DOWNLOADING);

        $department = Department::query()->where('online_id', $event->data['online_department_id'])->firstOrFail();
        $session = Session::query()->where('name', $event->data['session'])->firstOrFail();

        $students = $department->students()
            ->whereNotIn('status', StudentStatus::archivedStates())
            ->where('entry_session_id', $session->id)
            ->get();

        if ($students->isEmpty()) {
            $event->setMessage("No students found admitted in {$session->name} for {$department->name} ");
            $event->updateStatus(ImportEventStatus::FAILED);

            return Command::FAILURE;
        }

        [$numberOfData, $numberOfStudent, $failedMessage] = [0, 0, ''];

        foreach ($students as $student) {
            try {
                $data = $service->get($event->method, ['registration_number' => $student->registration_number]);

                $numberOfStudent += 1;
                $numberOfData += $data->count();

                $service->save($event, $data);
            } catch (Exception $e) {
                $failedMessage .= ("{$e->getMessage()}\n");

                continue;
            }
        }

        $event->updateMessageAndCounts($failedMessage, $numberOfStudent, $numberOfData);

        $event->updateStatus(ImportEventStatus::SAVED);

        Artisan::call('rp:process-portal-data', ['eventId' => $event->id]);

        return Command::SUCCESS;
    }
}
