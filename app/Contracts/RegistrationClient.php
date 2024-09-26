<?php

declare(strict_types=1);

namespace App\Contracts;

/**
 * @phpstan-type RegistrationDetail array{id:string, registration_number:string, session:string,semester:string,
 *     level:string, course_id:string, credit_unit:string, registration_date:string}
 */
interface RegistrationClient
{
    /** @return array<RegistrationDetail> */
    public function fetchRegistrationByRegistrationNumber(string $registrationNumber): array;

    /** @return array<RegistrationDetail> */
    public function fetchRegistrationByDepartmentSessionLevel(
        int $departmentId,
        string $session,
        int $level,
    ): array;

    /** @return array<RegistrationDetail> */
    public function fetchRegistrationByDepartmentSessionSemester(
        int $departmentId,
        string $session,
        string $semester,
    ): array;

    /** @return array<RegistrationDetail> */
    public function fetchRegistrationBySessionCourse(
        string $session,
        int $course,
    ): array;
}
