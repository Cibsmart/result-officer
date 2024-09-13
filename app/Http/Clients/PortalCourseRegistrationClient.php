<?php

declare(strict_types=1);

namespace App\Http\Clients;

use App\Contracts\CourseRegistrationClient;
use Config;

/** @phpstan-import-type CourseRegistrationDetail from \App\Contracts\CourseRegistrationClient */
final readonly class PortalCourseRegistrationClient extends ApiClient implements CourseRegistrationClient
{
    private string $endpoint;

    public function __construct()
    {
        $this->endpoint = Config::string('rp_http.endpoints.course_registrations');
    }

    /** {@inheritDoc}
     * @throws \Exception
     */
    public function fetchCourseRegistrationByRegistrationNumber(string $registrationNumber): array
    {
        /** @var array<CourseRegistrationDetail> $courses */
        $courses = $this->get(endpoint: $this->endpoint, parameters: ['registration_number' => $registrationNumber]);

        return $courses;
    }

    /** {@inheritDoc}
     * @throws \Exception
     */
    public function fetchCourseRegistrationByDepartmentSessionLevel(
        int $departmentId,
        string $session,
        int $level,
    ): array {
        /** @var array<CourseRegistrationDetail> $courses */
        $courses = $this->get(
            endpoint: $this->endpoint,
            parameters: ['department' => $departmentId, 'session' => $session, 'level' => $level],
        );

        return $courses;
    }

    /** {@inheritDoc}
     * @throws \Exception
     */
    public function fetchCourseRegistrationByDepartmentSessionSemester(
        int $departmentId,
        string $session,
        string $semester,
    ): array {
        /** @var array<CourseRegistrationDetail> $courses */
        $courses = $this->get(
            endpoint: $this->endpoint,
            parameters: ['department' => $departmentId, 'session' => $session, 'semester' => $semester],
        );

        return $courses;
    }

    /** {@inheritDoc}
     * @throws \Exception
     */
    public function fetchCourseRegistrationBySessionCourse(string $session, int $course): array
    {
        /** @var array<CourseRegistrationDetail> $courses */
        $courses = $this->get(endpoint: $this->endpoint, parameters: ['session' => $session, 'course_id' => $course]);

        return $courses;
    }
}
