<?php

declare(strict_types=1);

namespace App\Contracts;

/**
 * @phpstan-type ResultDetail array{id:string, course_registration_id: string, registration_number:string,
 *     in_course:string, exam_score:string, total_score:string, grade:string,
 *     upload_date:array{day:string,month:string, year:string}}
 */
interface ResultClient
{
    /** @return ResultDetail */
    public function fetchResultByCourseRegistrationId(string $courseRegistrationId): array;

    /** @return array<ResultDetail> */
    public function fetchResultsByRegistrationNumber(string $registrationNumber): array;

    /** @return array<ResultDetail> */
    public function fetchResultsByDepartmentSessionLevel(
        string $departmentId,
        string $session,
        string $level,
    ): array;

    /** @return array<ResultDetail> */
    public function fetchResultsByDepartmentSessionSemester(
        string $departmentId,
        string $session,
        string $semester,
    ): array;

    /** @return array<ResultDetail> */
    public function fetchResultsBySessionCourse(
        string $session,
        string $courseId,
    ): array;
}
