<?php

declare(strict_types=1);

namespace App\Http\Clients;

use App\Contracts\StudentClient;
use Config;

final readonly class PortalStudentClient extends ApiClient implements StudentClient
{
    private string $endpoint;

    public function __construct()
    {
        $this->endpoint = Config::string('rp_http.endpoints.students');
    }

    /**
     * @return array<string, string|array<string, string>>
     * @throws \Exception
     */
    public function fetchStudentByRegistrationNumber(string $registrationNumber): array
    {
        /** @var array<string, string|array<string, string>> $student */
        $student = $this->get(endpoint: $this->endpoint,
            parameters: ['registration_number' => $registrationNumber]);

        return $student;
    }

    /**
     * @return array<int, array<string, string|array<string, string>>>
     * @throws \Exception
     */
    public function fetchStudentsBySession(string $session): array
    {
        /** @var array<int, array<string, string|array<string, string>>> $students */
        $students = $this->get(endpoint: $this->endpoint, parameters: ['session' => $session]);

        return $students;
    }

    /**
     * @return array<int, array<string, string|array<string, string>>>
     * @throws \Exception
     */
    public function fetchStudentsByDepartmentAndSession(string $departmentId, string $session): array
    {
        /** @var array<int, array<string, string|array<string, string>>> $students */
        $students = $this->get(
            endpoint: $this->endpoint,
            parameters: ['department_id' => $departmentId, 'session' => $session],
        );

        return $students;
    }
}
