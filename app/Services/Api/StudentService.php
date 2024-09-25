<?php

declare(strict_types=1);

namespace App\Services\Api;

use App\Actions\Students\ProcessPortalStudent;
use App\Actions\Students\SavePortalStudent;
use App\Contracts\PortalService;
use App\Contracts\StudentClient;
use App\Data\Download\PortalStudentData;
use App\Enums\ImportEventMethod;
use App\Enums\RawDataStatus;
use App\Models\ImportEvent;
use Exception;
use Illuminate\Support\Collection;

/** @template-implements \App\Contracts\PortalService<\App\Data\Download\PortalStudentData> */
final readonly class StudentService implements PortalService
{
    public function __construct(
        private StudentClient $client,
        private SavePortalStudent $saveAction,
        private ProcessPortalStudent $processAction,
    ) {
    }

    /** @return \Illuminate\Support\Collection<int, \App\Data\Download\PortalStudentData> */
    public function getStudentByRegistrationNumber(string $registrationNumber): Collection
    {
        $student = $this->client->fetchStudentByRegistrationNumber($registrationNumber);

        return PortalStudentData::collect(collect($student));
    }

    /** @return \Illuminate\Support\Collection<int, \App\Data\Download\PortalStudentData> */
    public function getStudentsByDepartmentAndSession(int $departmentId, string $session): Collection
    {
        $students = $this->client->fetchStudentsByDepartmentAndSession($departmentId, $session);

        return PortalStudentData::collect(collect($students));
    }

    /** @return \Illuminate\Support\Collection<int, \App\Data\Download\PortalStudentData> */
    public function getStudentsBySession(string $session): Collection
    {
        $student = $this->client->fetchStudentsBySession($session);

        return PortalStudentData::collect(collect($student));
    }

    /**
     * {@inheritDoc}
     */
    public function get(ImportEventMethod $method, array $parameters): Collection
    {
        $session = $this->getValue('entry_session', $parameters);
        $department = (int) $this->getValue('online_department_id', $parameters);
        $registrationNumber = $this->getValue('registration_number', $parameters);

        return match ($method) {
            ImportEventMethod::REGISTRATION_NUMBER => $this->getStudentByRegistrationNumber($registrationNumber),
            ImportEventMethod::DEPARTMENT_SESSION => $this->getStudentsByDepartmentAndSession($department, $session),
            ImportEventMethod::SESSION => $this->getStudentsBySession($session),
            default => throw new Exception("Method {$method->value} not found")
        };
    }

    /** {@inheritDoc} */
    public function save(ImportEvent $event, Collection $data): void
    {
        foreach ($data as $student) {
            $this->saveAction->execute($event, $student);
        }
    }

    public function process(ImportEvent $event): void
    {
        $rawData = $event->students()->where('status', RawDataStatus::PENDING)->get();

        foreach ($rawData as $student) {
            try {
                $this->processAction->execute($student);
            } catch (Exception $e) {
                $student->setMessage($e->getMessage());

                $student->updateStatus(RawDataStatus::FAILED);
            }
        }
    }

    /** @param array<string, int|string> $data */
    private function getValue(string $key, array $data): string
    {
        return array_key_exists($key, $data)
            ? (string) $data[$key]
            : '';
    }
}
