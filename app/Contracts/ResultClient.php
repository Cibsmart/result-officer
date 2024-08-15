<?php

declare(strict_types=1);

namespace App\Contracts;

/** @phpstan-type ResultDetail array{id:string, course_registration_id: string, registration_number:string,
 *     in_course:string, exam_score:string, total_score:string, grade:string,
 *     upload_date:array{day:string,month:string, year:string}}
 */
interface ResultClient
{
    /** @return ResultDetail */
    public function fetchResultByCourseRegistrationId(string $onlineCourseRegistrationId): array;

    /** @return array<ResultDetail> */
    public function fetchResultsByRegistrationNumber(string $registrationNumber): array;

    /** @return array<ResultDetail> */
    public function fetchResultsByDepartmentSessionLevel(
        string $onlineDepartmentId,
        string $sessionName,
        string $levelName,
    ): array;

    /** @return array<ResultDetail> */
    public function fetchResultsByDepartmentSessionSemester(
        string $onlineDepartmentId,
        string $sessionName,
        string $semesterName,
    ): array;

    /** @return array<ResultDetail> */
    public function fetchResultsBySessionCourse(
        string $sessionName,
        string $onlineCourseId,
    ): array;
}
