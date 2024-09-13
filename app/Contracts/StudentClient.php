<?php

declare(strict_types=1);

namespace App\Contracts;

/**
 * phpcs:ignore SlevomatCodingStandard.Files.LineLength
 * @phpstan-type StudentDetail array{id:string, last_name:string, first_name:string, other_names:string,
 *     registration_number:string, gender:string, date_of_birth:string, department_id:string, option:string,
 *     state:string, local_government:string, entry_session:string, entry_mode:string, entry_level:string,
 *     jamb_registration_number:string, phone_number:string, email:string}
 */
interface StudentClient
{
    /** @return array<StudentDetail> */
    public function fetchStudentByRegistrationNumber(string $registrationNumber): array;

    /** @return array<StudentDetail> */
    public function fetchStudentsByDepartmentAndSession(int $departmentId, string $session): array;

    /** @return array<StudentDetail> */
    public function fetchStudentsBySession(string $session): array;
}
