<?php

declare(strict_types=1);

namespace App\Http\Clients;

use App\Contracts\StudentClient;
use Config;

/** @phpstan-import-type StudentDetail from \App\Contracts\StudentClient */
final readonly class PortalStudentClient extends ApiClient implements StudentClient
{
    private string $endpoint;

    public function __construct()
    {
        $this->endpoint = Config::string('rp_http.endpoints.students');
    }

    /**
     * {@inheritDoc}
     * @throws \Exception
     */
    public function fetchStudentByRegistrationNumber(string $registrationNumber): array
    {
        /** @var array<StudentDetail> $student */
        $student = $this->get(endpoint: $this->endpoint, parameters: ['registration_number' => $registrationNumber]);

        return $student;
    }

    /**
     * {@inheritDoc}
     * @throws \Exception
     */
    public function fetchStudentsBySession(string $session): array
    {
        /** @var array<StudentDetail> $students */
        $students = $this->get(endpoint: $this->endpoint, parameters: ['session' => $session]);

        return $students;
    }

    /**
     * {@inheritDoc}
     * @throws \Exception
     */
    public function fetchStudentsByDepartmentAndSession(int $departmentId, string $session): array
    {
        /** @var array<StudentDetail> $students */
        $students = $this->get(
            endpoint: $this->endpoint,
            parameters: ['department' => $departmentId, 'session' => $session],
        );

        return $students;
    }
}
