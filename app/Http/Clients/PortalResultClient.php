<?php

declare(strict_types=1);

namespace App\Http\Clients;

use App\Contracts\ResultClient;

/** @phpstan-import-type ResultDetail from \App\Contracts\ResultClient */
final class PortalResultClient extends ApiClient implements ResultClient
{
    /**
     * {@inheritDoc}
     * @throws \Exception
     */
    public function fetchResultByCourseRegistrationId(string $onlineCourseRegistrationId): array
    {
        $result = $this->get("results/course-registration/{$onlineCourseRegistrationId}");

        return $result;
    }

    /**
     * {@inheritDoc}
     * @throws \Exception
     */
    public function fetchResultsByRegistrationNumber(string $registrationNumber): array
    {
        /** @var array<ResultDetail> $result */
        $result = $this->get("results/registration-number/{$registrationNumber}");

        return $result;
    }

    /**
     * {@inheritDoc}
     * @throws \Exception
     */
    public function fetchResultsByDepartmentSessionLevel(
        string $onlineDepartmentId,
        string $sessionName,
        string $levelName,
    ): array {
        /** @var array<ResultDetail> $result */
        $result = $this->get("results/department/{$onlineDepartmentId}/session/{$sessionName}/level/{$levelName}");

        return $result;
    }

    /**
     * {@inheritDoc}
     * @throws \Exception
     */
    public function fetchResultsByDepartmentSessionSemester(
        string $onlineDepartmentId,
        string $sessionName,
        string $semesterName,
    ): array {
        /** @var array<ResultDetail> $result */
        $result = $this->get(
            "results/department/{$onlineDepartmentId}/session/{$sessionName}/semester/{$semesterName}",
        );

        return $result;
    }

    /**
     * {@inheritDoc}
     * @throws \Exception
     */
    public function fetchResultsBySessionCourse(string $sessionName, string $onlineCourseId): array
    {
        /** @var array<ResultDetail> $result */
        $result = $this->get("results/session/{$sessionName}/course/{$onlineCourseId}");

        return $result;
    }
}
