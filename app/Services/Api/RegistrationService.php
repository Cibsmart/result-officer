<?php

declare(strict_types=1);

namespace App\Services\Api;

use App\Contracts\CourseRegistrationClient;
use App\Data\Download\PortalRegistrationData;
use Illuminate\Support\Collection;

final class RegistrationService
{
    public function __construct(public CourseRegistrationClient $client)
    {
    }

    /** @return \Illuminate\Support\Collection<int, \App\Data\Download\PortalRegistrationData> */
    public function getCourseRegistrationsByRegistrationNumber(string $registrationNumber): Collection
    {
        $registrations = $this->client->fetchCourseRegistrationByRegistrationNumber($registrationNumber);

        return PortalRegistrationData::collect(collect($registrations));
    }

    /** @return \Illuminate\Support\Collection<int, \App\Data\Download\PortalRegistrationData> */
    public function getCourseRegistrationsByDepartmentSessionAndLevel(
        int $departmentId,
        string $session,
        int $level,
    ): Collection {
        $registrations = $this->client->fetchCourseRegistrationByDepartmentSessionLevel(
            $departmentId, $session, $level,
        );

        return PortalRegistrationData::collect(collect($registrations));
    }

    /** @return \Illuminate\Support\Collection<int, \App\Data\Download\PortalRegistrationData> */
    public function getCourseRegistrationsByDepartmentSessionAndSemester(
        int $departmentId,
        string $session,
        string $semester,
    ): Collection {
        $registrations = $this->client->fetchCourseRegistrationByDepartmentSessionSemester(
            $departmentId, $session, $semester,
        );

        return PortalRegistrationData::collect(collect($registrations));
    }

    /** @return \Illuminate\Support\Collection<int, \App\Data\Download\PortalRegistrationData> */
    public function getCourseRegistrationsBySessionAndCourse(string $session, int $courseId): Collection
    {
        $registrations = $this->client->fetchCourseRegistrationBySessionCourse($session, $courseId);

        return PortalRegistrationData::collect(collect($registrations));
    }
}
