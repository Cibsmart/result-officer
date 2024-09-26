<?php

declare(strict_types=1);

namespace App\Http\Clients;

use App\Contracts\RegistrationClient;
use Config;

/** @phpstan-import-type RegistrationDetail from \App\Contracts\RegistrationClient */
final readonly class PortalRegistrationClient extends ApiClient implements RegistrationClient
{
    private string $endpoint;

    public function __construct()
    {
        $this->endpoint = Config::string('rp_http.endpoints.course_registrations');
    }

    /** {@inheritDoc}
     * @throws \Exception
     */
    public function fetchRegistrationByRegistrationNumber(string $registrationNumber): array
    {
        /** @var array<RegistrationDetail> $courses */
        $courses = $this->get(endpoint: $this->endpoint, parameters: ['registration_number' => $registrationNumber]);

        return $courses;
    }

    /** {@inheritDoc}
     * @throws \Exception
     */
    public function fetchRegistrationByDepartmentSessionLevel(
        int $departmentId,
        string $session,
        int $level,
    ): array {
        /** @var array<RegistrationDetail> $courses */
        $courses = $this->get(
            endpoint: $this->endpoint,
            parameters: ['department' => $departmentId, 'session' => $session, 'level' => $level],
        );

        return $courses;
    }

    /** {@inheritDoc}
     * @throws \Exception
     */
    public function fetchRegistrationByDepartmentSessionSemester(
        int $departmentId,
        string $session,
        string $semester,
    ): array {
        /** @var array<RegistrationDetail> $courses */
        $courses = $this->get(
            endpoint: $this->endpoint,
            parameters: ['department' => $departmentId, 'session' => $session, 'semester' => $semester],
        );

        return $courses;
    }

    /** {@inheritDoc}
     * @throws \Exception
     */
    public function fetchRegistrationBySessionCourse(string $session, int $course): array
    {
        /** @var array<RegistrationDetail> $courses */
        $courses = $this->get(endpoint: $this->endpoint, parameters: ['session' => $session, 'course_id' => $course]);

        return $courses;
    }
}
