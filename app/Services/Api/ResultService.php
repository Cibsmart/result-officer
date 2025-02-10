<?php

declare(strict_types=1);

namespace App\Services\Api;

use App\Actions\Imports\Results\ProcessPortalResult;
use App\Actions\Imports\Results\SavePortalResult;
use App\Contracts\PortalService;
use App\Contracts\ResultClient;
use App\Data\Download\PortalResultData;
use App\Enums\ImportEventMethod;
use App\Enums\RawDataStatus;
use App\Models\ImportEvent;
use Exception;
use Illuminate\Support\Collection;

/** @implements \App\Contracts\PortalService<\App\Data\Download\PortalResultData> */
final readonly class ResultService implements PortalService
{
    public function __construct(
        private ResultClient $client,
        public SavePortalResult $saveAction,
        public ProcessPortalResult $processAction,
    ) {
    }

    /** @return \Illuminate\Support\Collection<int, \App\Data\Download\PortalResultData> */
    public function getResultByCourseRegistrationId(int $courseRegistrationId): Collection
    {
        $result = $this->client->fetchResultByCourseRegistrationId($courseRegistrationId);

        return PortalResultData::collect(collect($result));
    }

    /** @return \Illuminate\Support\Collection<int, \App\Data\Download\PortalResultData> */
    public function getResultsByRegistrationNumber(string $registrationNumber): Collection
    {
        $results = $this->client->fetchResultsByRegistrationNumber($registrationNumber);

        return PortalResultData::collect(collect($results));
    }

    /** @return \Illuminate\Support\Collection<int, \App\Data\Download\PortalResultData> */
    public function getResultsByDepartmentSessionAndLevel(
        int $departmentId,
        string $session,
        int $level,
    ): Collection {
        $results = $this->client->fetchResultsByDepartmentSessionLevel($departmentId, $session, $level);

        return PortalResultData::collect(collect($results));
    }

    /** @return \Illuminate\Support\Collection<int, \App\Data\Download\PortalResultData> */
    public function getResultsByDepartmentSessionAndSemester(
        int $departmentId,
        string $session,
        string $semester,
    ): Collection {

        $results = $this->client->fetchResultsByDepartmentSessionSemester($departmentId, $session, $semester);

        return PortalResultData::collect(collect($results));
    }

    /** @return \Illuminate\Support\Collection<int, \App\Data\Download\PortalResultData> */
    public function getResultsBySessionAndCourse(
        string $session,
        int $course,
    ): Collection {

        $results = $this->client->fetchResultsBySessionCourse($session, $course);

        return PortalResultData::collect(collect($results));
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
            ImportEventMethod::SESSION_COURSE => $this->getResultsBySessionAndCourse($session, $course),
            ImportEventMethod::SESSION,
            ImportEventMethod::REGISTRATION_NUMBER,
            ImportEventMethod::DEPARTMENT_SESSION => $this->getResultsByRegistrationNumber($registrationNumber),
            ImportEventMethod::DEPARTMENT_SESSION_LEVEL => $this->getResultsByDepartmentSessionAndLevel(
                $department,
                $session,
                $level,
            ),
            ImportEventMethod::DEPARTMENT_SESSION_SEMESTER => $this->getResultsByDepartmentSessionAndSemester(
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
        foreach ($data as $student) {
            $this->saveAction->execute($event, $student);
        }
    }

    public function process(ImportEvent $event): void
    {
        $rawData = $event->results()
            ->where('status', RawDataStatus::PENDING)
            ->lazyById();

        foreach ($rawData as $result) {
            try {
                $this->processAction->execute($result);
            } catch (Exception $e) {
                $result->setMessage($e->getMessage());

                $result->updateStatus(RawDataStatus::FAILED);
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
