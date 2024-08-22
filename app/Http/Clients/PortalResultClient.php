<?php

declare(strict_types=1);

namespace App\Http\Clients;

use App\Contracts\ResultClient;
use Config;

/** @phpstan-import-type ResultDetail from \App\Contracts\ResultClient */
final readonly class PortalResultClient extends ApiClient implements ResultClient
{
    private string $endpoint;

    public function __construct()
    {
        $this->endpoint = Config::string('rp_http.endpoints.results');
    }

    /**
     * {@inheritDoc}
     * @throws \Exception
     */
    public function fetchResultByCourseRegistrationId(string $courseRegistrationId): array
    {
        /** @var array<ResultDetail> $result */
        $result = $this->get(
            endpoint: $this->endpoint,
            parameters: ['course_registration_id' => $courseRegistrationId],
        );

        return $result;
    }

    /**
     * {@inheritDoc}
     * @throws \Exception
     */
    public function fetchResultsByRegistrationNumber(string $registrationNumber): array
    {
        /** @var array<ResultDetail> $result */
        $result = $this->get(endpoint: $this->endpoint,
            parameters: ['registration_number' => $registrationNumber]);

        return $result;
    }

    /**
     * {@inheritDoc}
     * @throws \Exception
     */
    public function fetchResultsByDepartmentSessionLevel(
        string $departmentId,
        string $session,
        string $level,
    ): array {
        /** @var array<ResultDetail> $result */
        $result = $this->get(
            endpoint: $this->endpoint,
            parameters: ['department_id' => $departmentId, 'session' => $session, 'level' => $level],
        );

        return $result;
    }

    /**
     * {@inheritDoc}
     * @throws \Exception
     */
    public function fetchResultsByDepartmentSessionSemester(
        string $departmentId,
        string $session,
        string $semester,
    ): array {
        /** @var array<ResultDetail> $result */
        $result = $this->get(
            endpoint: $this->endpoint,
            parameters: ['department_id' => $departmentId, 'session' => $session, 'semester' => $semester],
        );

        return $result;
    }

    /**
     * {@inheritDoc}
     * @throws \Exception
     */
    public function fetchResultsBySessionCourse(string $session, string $courseId): array
    {
        /** @var array<ResultDetail> $result */
        $result = $this->get(
            endpoint: $this->endpoint,
            parameters: ['session' => $session, 'course_id' => $courseId],
        );

        return $result;
    }
}
