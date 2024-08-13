<?php

declare(strict_types=1);

namespace App\Contracts;

interface CourseRegistrationClient
{
    /** @return array<string, string|array{day: string, month:string, year:string}> */
    public function fetchCourseRegistrationByRegistrationNumber(string $registrationNumber): array;

    /** @return array<string, string|array{day: string, month:string, year:string}> */
    public function fetchCourseRegistrationByDepartmentSessionLevel(
        string $departmentId,
        string $session,
        string $level,
    ): array;

    /** @return array<string, string|array{day: string, month:string, year:string}> */
    public function fetchCourseRegistrationByDepartmentSessionSemester(
        string $departmentId,
        string $session,
        string $semester,
    ): array;

    /** @return array<string, string|array{day: string, month:string, year:string}> */
    public function fetchCourseRegistrationBySessionCourse(
        string $session,
        string $course,
    ): array;
}
