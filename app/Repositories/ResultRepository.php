<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Classes\PendingResult;
use App\Data\Download\PortalResultData;
use App\Data\Response\ResponseData;
use App\Models\CourseRegistration;
use App\Services\Api\ResultService;
use Exception;
use Illuminate\Support\Collection;

final readonly class ResultRepository
{
    public function __construct(public ResultService $service)
    {
    }

    public function getResultByCourseRegistrationId(int $courseRegistrationId): PortalResultData
    {
        $results = $this->service->getResultByCourseRegistrationId($courseRegistrationId)->first();

        assert($results instanceof PortalResultData);

        return $results;
    }

    /** @return \Illuminate\Support\Collection<int, \App\Data\Download\PortalResultData> */
    public function getResultByRegistrationNumber(string $registrationNumber): Collection
    {
        return $this->service->getResultsByRegistrationNumber($registrationNumber);
    }

    /** @return \Illuminate\Support\Collection<int, \App\Data\Download\PortalResultData> */
    public function getResultByDepartmentSessionAndLevel(
        int $departmentId,
        string $session,
        int $level,
    ): Collection {
        return $this->service->getResultsByDepartmentSessionAndLevel($departmentId, $session, $level);
    }

    /** @return \Illuminate\Support\Collection<int, \App\Data\Download\PortalResultData> */
    public function getResultByDepartmentSessionAndSemester(
        int $departmentId,
        string $session,
        string $semester,
    ): Collection {
        return $this->service->getResultsByDepartmentSessionAndSemester($departmentId, $session, $semester);
    }

    /** @return \Illuminate\Support\Collection<int, \App\Data\Download\PortalResultData> */
    public function getResultBySessionAndCourse(string $session, int $course): Collection
    {
        return $this->service->getResultsBySessionAndCourse($session, $course);
    }

    /**
     * @param \Illuminate\Support\Collection<int, \App\Data\Download\PortalResultData> $results
     * @return \Illuminate\Support\Collection<int, \App\Data\Response\ResponseData>
     * @throws \Exception
     */
    public function saveResults(Collection $results): Collection
    {
        $courseRegistrations = CourseRegistration::query()
            ->with('result')
            ->whereIn('online_id', $results->pluck('courseRegistrationId'))
            ->get();

        $responses = [];

        foreach ($results as $result) {

            $courseRegistration = $courseRegistrations->where('online_id', $result->onlineId)->first();

            try {
                $this->saveResult($courseRegistration, $result);

                $responses[] = ResponseData::from([$result->registrationNumber, true]);
            } catch (Exception $e) {
                $responses[] = ResponseData::from([$result->registrationNumber, $e->getMessage()]);

                continue;
            }
        }

        return ResponseData::collect(collect($responses));
    }

    /** @throws \Exception */
    public function saveResult(?CourseRegistration $courseRegistration, PortalResultData $data): bool
    {
        if (is_null($courseRegistration)) {
            throw new Exception('COURSE REGISTRATION NOT FOUND: Download course registration records and try again');
        }

        $pendingResult = PendingResult::new(courseRegistration: $courseRegistration, resultData: $data);

        return $pendingResult->save();
    }
}
