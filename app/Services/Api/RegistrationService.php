<?php

declare(strict_types=1);

namespace App\Services\Api;

use App\Actions\Import\Registrations\ProcessPortalRegistration;
use App\Actions\Import\Registrations\SavePortalRegistration;
use App\Contracts\PortalService;
use App\Contracts\RegistrationClient;
use App\Data\Download\PortalRegistrationData;
use App\Enums\ImportEventMethod;
use App\Enums\RawDataStatus;
use App\Models\ImportEvent;
use Exception;
use Illuminate\Support\Collection;

/** @implements \App\Contracts\PortalService<\App\Data\Download\PortalRegistrationData> */
final readonly class RegistrationService implements PortalService
{
    public function __construct(
        public RegistrationClient $client,
        public SavePortalRegistration $saveAction,
        public ProcessPortalRegistration $processAction,
    ) {
    }

    /** @return \Illuminate\Support\Collection<int, \App\Data\Download\PortalRegistrationData> */
    public function getRegistrationsByRegistrationNumber(string $registrationNumber): Collection
    {
        $registrations = $this->client->fetchRegistrationByRegistrationNumber($registrationNumber);

        return PortalRegistrationData::collect(collect($registrations));
    }

    /** @return \Illuminate\Support\Collection<int, \App\Data\Download\PortalRegistrationData> */
    public function getRegistrationsByDepartmentSessionAndLevel(
        int $departmentId,
        string $session,
        int $level,
    ): Collection {
        $registrations = $this->client->fetchRegistrationByDepartmentSessionLevel($departmentId, $session, $level);

        return PortalRegistrationData::collect(collect($registrations));
    }

    /** @return \Illuminate\Support\Collection<int, \App\Data\Download\PortalRegistrationData> */
    public function getRegistrationsByDepartmentSessionAndSemester(
        int $departmentId,
        string $session,
        string $semester,
    ): Collection {
        $registrations = $this->client->fetchRegistrationByDepartmentSessionSemester(
            $departmentId, $session, $semester,
        );

        return PortalRegistrationData::collect(collect($registrations));
    }

    /** @return \Illuminate\Support\Collection<int, \App\Data\Download\PortalRegistrationData> */
    public function getRegistrationsBySessionAndCourse(string $session, int $courseId): Collection
    {
        $registrations = $this->client->fetchRegistrationBySessionCourse($session, $courseId);

        return PortalRegistrationData::collect(collect($registrations));
    }

    /** {@inheritDoc} */
    public function get(ImportEventMethod $method, array $parameters): Collection
    {
        $course = (int) $this->getValue('online_course_id', $parameters);
        $level = (int) $this->getValue('level', $parameters);
        $semester = $this->getValue('semester', $parameters);
        $session = $this->getValue('session', $parameters);
        $department = (int) $this->getValue('online_department_id', $parameters);
        $registrationNumber = $this->getValue('registration_number', $parameters);

        return match ($method) {
            ImportEventMethod::SESSION_COURSE => $this->getRegistrationsBySessionAndCourse($session, $course),
            ImportEventMethod::SESSION,
            ImportEventMethod::REGISTRATION_NUMBER,
            ImportEventMethod::DEPARTMENT_SESSION => $this->getRegistrationsByRegistrationNumber($registrationNumber),
            ImportEventMethod::DEPARTMENT_SESSION_LEVEL => $this->getRegistrationsByDepartmentSessionAndLevel(
                $department,
                $session,
                $level,
            ),
            ImportEventMethod::DEPARTMENT_SESSION_SEMESTER => $this->getRegistrationsByDepartmentSessionAndSemester(
                $department,
                $session,
                $semester,
            ),
            default => throw new Exception("Method {$method->value} not found")
        };
    }

    /** {@inheritDoc} */
    public function save(ImportEvent $event, Collection $data): void
    {
        foreach ($data as $registration) {
            $this->saveAction->execute($event, $registration);
        }
    }

    public function process(ImportEvent $event): void
    {
        $rawData = $event->registrations()
            ->where('status', RawDataStatus::PENDING)
            ->lazyById();

        foreach ($rawData as $registration) {
            try {
                $this->processAction->execute($registration);
            } catch (Exception $e) {
                $registration->setMessage($e->getMessage());

                $registration->updateStatus(RawDataStatus::FAILED);
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
