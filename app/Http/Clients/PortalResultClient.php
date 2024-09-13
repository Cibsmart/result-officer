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
    public function fetchResultByCourseRegistrationId(int $courseRegistrationId): array
    {
        /** @var array<ResultDetail> $result */
        $result = $this->get(
            endpoint: $this->endpoint,
            parameters: ['courses_registered_id' => $courseRegistrationId],
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
        int $departmentId,
        string $session,
        int $level,
    ): array {
        /** @var array<ResultDetail> $result */
        $result = $this->get(
            endpoint: $this->endpoint,
            parameters: ['department' => $departmentId, 'session' => $session, 'level' => $level],
        );

        return $result;
    }

    /**
     * {@inheritDoc}
     * @throws \Exception
     */
    public function fetchResultsByDepartmentSessionSemester(
        int $departmentId,
        string $session,
        string $semester,
    ): array {
        /** @var array<ResultDetail> $result */
        $result = $this->get(
            endpoint: $this->endpoint,
            parameters: ['department' => $departmentId, 'session' => $session, 'semester' => $semester],
        );

        return $result;
    }

    /**
     * {@inheritDoc}
     * @throws \Exception
     */
    public function fetchResultsBySessionCourse(string $session, int $courseId): array
    {
        /** @var array<ResultDetail> $result */
        $result = $this->get(
            endpoint: $this->endpoint,
            parameters: ['session' => $session, 'course_id' => $courseId],
        );

        return $result;
    }

    /**
     * {@inheritDoc}
     * @throws \Exception
     */
    public function fetchResultsByRegistrationNumberSessionAndSemester(
        string $registrationNumber,
        string $session,
        string $semester,
    ): array {
        /** @var array<ResultDetail> $result */
        $result = $this->get(endpoint: $this->endpoint,
            parameters: [
                'registration_number' => $registrationNumber,
                'semester' => $semester,
                'session' => $session,
            ]);

        return $result;
    }
}
