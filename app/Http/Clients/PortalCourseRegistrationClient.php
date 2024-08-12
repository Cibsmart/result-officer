<?php

declare(strict_types=1);

namespace App\Http\Clients;

use App\Contracts\CourseRegistration;

final class PortalCourseRegistrationClient extends ApiClient implements CourseRegistration
{
    /** {@inheritDoc}
     * @throws \Exception
     */
    public function fetchCourseRegistrationByRegistrationNumber(string $registrationNumber): array
    {
        /** @var array<string, string|array{day: string, month:string, year:string}> $courses */
        $courses = $this->get("course-registrations/registration-number/{$registrationNumber}");

        return $courses;
    }

    /** {@inheritDoc}
     * @throws \Exception
     */
    public function fetchCourseRegistrationByDepartmentSessionLevel(
        string $departmentId,
        string $session,
        string $level,
    ): array {
        /** @var array<string, string|array{day: string, month:string, year:string}> $courses */
        $courses = $this->get("course-registrations/department/{$departmentId}/session/{$session}/level/{$level}");

        return $courses;
    }

    /** {@inheritDoc}
     * @throws \Exception
     */
    public function fetchCourseRegistrationByDepartmentSessionSemester(
        string $departmentId,
        string $session,
        string $semester,
    ): array {
        /** @var array<string, string|array{day: string, month:string, year:string}> $courses */
        $courses = $this->get(
            "course-registrations/department/{$departmentId}/session/{$session}/semester/{$semester}",
        );

        return $courses;
    }

    /** {@inheritDoc}
     * @throws \Exception
     */
    public function fetchCourseRegistrationBySessionCourse(string $session, string $course): array
    {
        /** @var array<string, string|array{day: string, month:string, year:string}> $courses */
        $courses = $this->get("course-registrations/session/{$session}/course/{$course}");

        return $courses;
    }
}
