<?php

declare(strict_types=1);

namespace App\Services\Api;

use App\Contracts\RegistrationClient;
use App\Data\Download\PortalRegistrationData;
use Illuminate\Support\Collection;

final class RegistrationService
{
    public function __construct(public RegistrationClient $client)
    {
    }

    /** @return \Illuminate\Support\Collection<int, \App\Data\Download\PortalRegistrationData> */
    public function getRegistrationsByRegistrationNumber(string $registrationNumber): Collection
    {
        $registrations = $this->client->fetchRegistrationByRegistrationNumber($registrationNumber);

        return PortalRegistrationData::collect(collect($registrations));
    }

    /** @return \Illuminate\Support\Collection<int, \App\Data\Download\PortalRegistrationData> */
    public function getRegistrationsByDepartmentSessionAndLevel(
        int $departmentId,
        string $session,
        int $level,
    ): Collection {
        $registrations = $this->client->fetchRegistrationByDepartmentSessionLevel($departmentId, $session, $level);

        return PortalRegistrationData::collect(collect($registrations));
    }

    /** @return \Illuminate\Support\Collection<int, \App\Data\Download\PortalRegistrationData> */
    public function getRegistrationsByDepartmentSessionAndSemester(
        int $departmentId,
        string $session,
        string $semester,
    ): Collection {
        $registrations = $this->client->fetchRegistrationByDepartmentSessionSemester(
            $departmentId, $session, $semester,
        );

        return PortalRegistrationData::collect(collect($registrations));
    }

    /** @return \Illuminate\Support\Collection<int, \App\Data\Download\PortalRegistrationData> */
    public function getRegistrationsBySessionAndCourse(string $session, int $courseId): Collection
    {
        $registrations = $this->client->fetchRegistrationBySessionCourse($session, $courseId);

        return PortalRegistrationData::collect(collect($registrations));
    }
}
