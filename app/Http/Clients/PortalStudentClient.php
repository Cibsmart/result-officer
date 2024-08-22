<?php

declare(strict_types=1);

namespace App\Http\Clients;

use App\Contracts\StudentClient;

final readonly class PortalStudentClient extends ApiClient implements StudentClient
{
    /**
     * @return array<string, string|array<string, string>>
     * @throws \Exception
     */
    public function fetchStudentByRegistrationNumber(string $registrationNumber): array
    {
        /** @var array<string, string|array<string, string>> $student */
        $student = $this->get("students/registration-number/$registrationNumber");

        return $student;
    }

    /**
     * @return array<int, array<string, string|array<string, string>>>
     * @throws \Exception
     */
    public function fetchStudentsBySession(string $session): array
    {
        /** @var array<int, array<string, string|array<string, string>>> $students */
        $students = $this->get("students/session/$session");

        return $students;
    }

    /**
     * @return array<int, array<string, string|array<string, string>>>
     * @throws \Exception
     */
    public function fetchStudentsByDepartmentAndSession(string $departmentId, string $session): array
    {
        /** @var array<int, array<string, string|array<string, string>>> $students */
        $students = $this->get("students/department/$departmentId/session/$session");

        return $students;
    }
}
