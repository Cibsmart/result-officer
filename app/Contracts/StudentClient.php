<?php

declare(strict_types=1);

namespace App\Contracts;

interface StudentClient
{
    /** @return array<string, string|array<string, string>> */
    public function fetchStudentByRegistrationNumber(string $registrationNumber): array;

    /** @return array<int, array<string, string|array<string, string>>> */
    public function fetchStudentsByDepartmentAndSession(string $departmentId, string $session): array;

    /** @return array<int, array<string, string|array<string, string>>> */
    public function fetchStudentsBySession(string $session): array;
}
