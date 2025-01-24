<?php

declare(strict_types=1);

namespace App\Console\Commands\ImportPortalData;

use App\Enums\ImportEventMethod;
use App\Enums\ImportEventStatus;
use App\Enums\ImportEventType;
use App\Enums\StudentStatus;
use App\Models\Department;
use App\Models\ImportEvent;
use App\Models\Session;
use App\Models\Student;
use App\Services\Api\PortalServiceFactory;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Artisan;

final class ImportGroupPortalData extends Command
{
    protected $signature = 'rp:import-group-portal-data {eventId}';

    protected $description = 'Import Data from the Portal per Student and Save in the Raw Data Table in the Database';

    public function handle(PortalServiceFactory $factory): int
    {
        $event = ImportEvent::findOrFail($this->argument('eventId'));

        if ($event->type === ImportEventType::STUDENTS) {

            Artisan::call('rp:import-portal-data', ['eventId' => $event->id]);

            return Command::SUCCESS;
        }

        $service = $factory->resolve($event->type);

        $event->updateStatus(ImportEventStatus::DOWNLOADING);

        $students = $this->getStudents($event);

        if ($students->isEmpty()) {
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
                $failedMessage .= ("{$student->registration_number}: {$e->getMessage()}\n");

                continue;
            }
        }

        $event->updateMessageAndCounts($failedMessage, $numberOfStudent, $numberOfData);

        $event->updateStatus(ImportEventStatus::SAVED);

        Artisan::call('rp:process-portal-data', ['eventId' => $event->id]);

        return Command::SUCCESS;
    }

    /** @return \Illuminate\Database\Eloquent\Collection<int, \App\Models\Student> */
    private function getStudents(ImportEvent $event): Collection
    {
        $session = Session::query()->where('name', $event->data['session'])->firstOrFail();

        $students = $event->method === ImportEventMethod::DEPARTMENT_SESSION
            ? $this->getStudentsByEntrySessionAndDepartment($event, $session)
            : $this->getStudentsByEntrySession($session);

        if ($students->isEmpty()) {
            $event->setMessage('No students found admitted');
            $event->updateStatus(ImportEventStatus::FAILED);
        }

        return $students;
    }

    /** @return \Illuminate\Database\Eloquent\Collection<int, \App\Models\Student> */
    private function getStudentsByEntrySession(Session $session): Collection
    {
        return Student::query()
            ->whereNotIn('status', StudentStatus::archivedStates())
            ->where('entry_session_id', $session->id)
            ->get();
    }

    /** @return \Illuminate\Database\Eloquent\Collection<int, \App\Models\Student> */
    private function getStudentsByEntrySessionAndDepartment(
        ImportEvent $event,
        Session $session,
    ): Collection {

        $department = Department::query()
            ->where('online_id', $event->data['online_department_id'])
            ->firstOrFail();

        return $department->students()
            ->whereNotIn('status', StudentStatus::archivedStates())
            ->where('entry_session_id', $session->id)
            ->get();
    }
}
