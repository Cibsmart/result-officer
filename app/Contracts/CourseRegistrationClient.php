<?php

declare(strict_types=1);

namespace App\Contracts;

/**
 * @phpstan-type CourseRegistrationDetail array{id:string, registration_number:string, session:string,semester:string,
 *     level:string, course_id:string, credit_unit:string, registration_date:array{day:string,month:string,
 *     year:string}}
 */
interface CourseRegistrationClient
{
    /** @return array<CourseRegistrationDetail> */
    public function fetchCourseRegistrationByRegistrationNumber(string $registrationNumber): array;

    /** @return array<CourseRegistrationDetail> */
    public function fetchCourseRegistrationByDepartmentSessionLevel(
        string $departmentId,
        string $session,
        string $level,
    ): array;

    /** @return array<CourseRegistrationDetail> */
    public function fetchCourseRegistrationByDepartmentSessionSemester(
        string $departmentId,
        string $session,
        string $semester,
    ): array;

    /** @return array<CourseRegistrationDetail> */
    public function fetchCourseRegistrationBySessionCourse(
        string $session,
        string $course,
    ): array;
}
