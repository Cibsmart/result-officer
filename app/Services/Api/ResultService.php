<?php

declare(strict_types=1);

namespace App\Services\Api;

use App\Contracts\ResultClient;
use App\Data\Download\PortalResultData;
use Illuminate\Support\Collection;

final readonly class ResultService
{
    public function __construct(private ResultClient $client)
    {
    }

    /** @return \Illuminate\Support\Collection<int, \App\Data\Download\PortalResultData> */
    public function getResultByCourseRegistrationId(string $courseRegistrationId): Collection
    {
        $result = $this->client->fetchResultByCourseRegistrationId($courseRegistrationId);

        return PortalResultData::collect(collect($result));
    }

    /** @return \Illuminate\Support\Collection<int, \App\Data\Download\PortalResultData> */
    public function getResultsByRegistrationNumber(string $registrationNumber): Collection
    {
        $results = $this->client->fetchResultsByRegistrationNumber($registrationNumber);

        return PortalResultData::collect(collect($results));
    }

    /** @return \Illuminate\Support\Collection<int, \App\Data\Download\PortalResultData> */
    public function getResultsByDepartmentSessionAndLevel(
        string $departmentId,
        string $session,
        string $level,
    ): Collection {
        $results = $this->client->fetchResultsByDepartmentSessionLevel($departmentId, $session, $level);

        return PortalResultData::collect(collect($results));
    }

    /** @return \Illuminate\Support\Collection<int, \App\Data\Download\PortalResultData> */
    public function getResultsByDepartmentSessionAndSemester(
        string $departmentId,
        string $session,
        string $semester,
    ): Collection {

        $results = $this->client->fetchResultsByDepartmentSessionSemester($departmentId, $session, $semester);

        return PortalResultData::collect(collect($results));
    }

    /** @return \Illuminate\Support\Collection<int, \App\Data\Download\PortalResultData> */
    public function getResultsBySessionAndCourse(
        string $session,
        string $course,
    ): Collection {

        $results = $this->client->fetchResultsBySessionCourse($session, $course);

        return PortalResultData::collect(collect($results));
    }
}
