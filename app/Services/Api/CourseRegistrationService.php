<?php

declare(strict_types=1);

namespace App\Services\Api;

use App\Contracts\CourseRegistrationClient;
use App\Data\Download\PortalCourseRegistrationData;
use Illuminate\Support\Collection;

final class CourseRegistrationService
{
    public function __construct(public CourseRegistrationClient $client)
    {
    }

    /** @return \Illuminate\Support\Collection<int, \App\Data\Download\PortalCourseRegistrationData> */
    public function getCourseRegistrationsByRegistrationNumber(string $registrationNumber): Collection
    {
        $registrations = $this->client->fetchCourseRegistrationByRegistrationNumber($registrationNumber);

        return PortalCourseRegistrationData::collect(collect($registrations));
    }

    /** @return \Illuminate\Support\Collection<int, \App\Data\Download\PortalCourseRegistrationData> */
    public function getCourseRegistrationsByDepartmentSessionAndLevel(
        string $departmentId,
        string $session,
        string $level,
    ): Collection {
        $registrations = $this->client->fetchCourseRegistrationByDepartmentSessionLevel(
            $departmentId, $session, $level,
        );

        return PortalCourseRegistrationData::collect(collect($registrations));
    }

    /** @return \Illuminate\Support\Collection<int, \App\Data\Download\PortalCourseRegistrationData> */
    public function getCourseRegistrationsByDepartmentSessionAndSemester(
        string $departmentId,
        string $session,
        string $semester,
    ): Collection {
        $registrations = $this->client->fetchCourseRegistrationByDepartmentSessionSemester(
            $departmentId, $session, $semester,
        );

        return PortalCourseRegistrationData::collect(collect($registrations));
    }

    /** @return \Illuminate\Support\Collection<int, \App\Data\Download\PortalCourseRegistrationData> */
    public function getCourseRegistrationsBySessionAndCourse(string $session, string $courseId): Collection
    {
        $registrations = $this->client->fetchCourseRegistrationBySessionCourse($session, $courseId);

        return PortalCourseRegistrationData::collect(collect($registrations));
    }
}
