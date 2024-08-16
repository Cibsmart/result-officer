<?php

declare(strict_types=1);

namespace App\Http\Clients;

use App\Contracts\ResultClient;
use Illuminate\Support\Str;

/** @phpstan-import-type ResultDetail from \App\Contracts\ResultClient */
final class PortalResultClient extends ApiClient implements ResultClient
{
    /**
     * {@inheritDoc}
     * @throws \Exception
     */
    public function fetchResultByCourseRegistrationId(string $courseRegistrationId): array
    {
        return $this->get("results/course-registration/{$courseRegistrationId}");
    }

    /**
     * {@inheritDoc}
     * @throws \Exception
     */
    public function fetchResultsByRegistrationNumber(string $registrationNumber): array
    {
        $registrationNumber = Str::replace('/', '-', $registrationNumber);

        /** @var array<ResultDetail> $result */
        $result = $this->get("results/registration-number/{$registrationNumber}");

        return $result;
    }

    /**
     * {@inheritDoc}
     * @throws \Exception
     */
    public function fetchResultsByDepartmentSessionLevel(
        string $departmentId,
        string $session,
        string $level,
    ): array {
        $session = Str::replace('/', '-', $session);

        /** @var array<ResultDetail> $result */
        $result = $this->get("results/department/{$departmentId}/session/{$session}/level/{$level}");

        return $result;
    }

    /**
     * {@inheritDoc}
     * @throws \Exception
     */
    public function fetchResultsByDepartmentSessionSemester(
        string $departmentId,
        string $session,
        string $semester,
    ): array {
        $session = Str::replace('/', '-', $session);

        /** @var array<ResultDetail> $result */
        $result = $this->get("results/department/{$departmentId}/session/{$session}/semester/{$semester}");

        return $result;
    }

    /**
     * {@inheritDoc}
     * @throws \Exception
     */
    public function fetchResultsBySessionCourse(string $session, string $courseId): array
    {
        $session = Str::replace('/', '-', $session);

        /** @var array<ResultDetail> $result */
        $result = $this->get("results/session/{$session}/course/{$courseId}");

        return $result;
    }
}
