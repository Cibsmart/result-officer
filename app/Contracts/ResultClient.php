<?php

declare(strict_types=1);

namespace App\Contracts;

/**
 * @phpstan-type ResultDetail array{id:string, course_registration_id: string, registration_number:string,
 *     in_course:string, exam_score:string, total_score:string, grade:string, upload_date:string,
 *     exam_date?:string, lecturer_name?:string, lecturer_email?:string, lecturer_phone_number?:string,
 *     lecturer_department?:string}
 */
interface ResultClient
{
    /** @return array<ResultDetail> */
    public function fetchResultByCourseRegistrationId(int $courseRegistrationId): array;

    /** @return array<ResultDetail> */
    public function fetchResultsByRegistrationNumber(string $registrationNumber): array;

    /** @return array<ResultDetail> */
    public function fetchResultsByRegistrationNumberSessionAndSemester(
        string $registrationNumber,
        string $session,
        string $semester,
    ): array;

    /** @return array<ResultDetail> */
    public function fetchResultsByDepartmentSessionLevel(
        int $departmentId,
        string $session,
        int $level,
    ): array;

    /** @return array<ResultDetail> */
    public function fetchResultsByDepartmentSessionSemester(
        int $departmentId,
        string $session,
        string $semester,
    ): array;

    /** @return array<ResultDetail> */
    public function fetchResultsBySessionCourse(
        string $session,
        int $courseId,
    ): array;
}
