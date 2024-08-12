<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Classes\PendingCourseRegistration;
use App\Data\Download\PortalCourseRegistrationData;
use App\Data\Response\ResponseData;
use App\Models\CourseRegistration;
use App\Services\Api\CourseRegistrationService;
use Exception;
use Illuminate\Support\Collection;

final class CourseRegistrationRepository
{
    public function __construct(public CourseRegistrationService $service)
    {
    }

    /** @return \Illuminate\Support\Collection<int, \App\Data\Download\PortalCourseRegistrationData> */
    public function getCourseRegistrationsByRegistrationNumber(string $registrationNumber): Collection
    {
        return $this->service->getCourseRegistrationsByRegistrationNumber($registrationNumber);
    }

    /** @return \Illuminate\Support\Collection<int, \App\Data\Download\PortalCourseRegistrationData> */
    public function getCourseRegistrationsByDepartmentAndSessionLevel(
        string $departmentId,
        string $session,
        string $level,
    ): Collection {
        return $this->service->getCourseRegistrationsByDepartmentSessionAndLevel($departmentId, $session, $level);
    }

    /** @return \Illuminate\Support\Collection<int, \App\Data\Download\PortalCourseRegistrationData> */
    public function getCourseRegistrationsByDepartmentSessionAndSemester(
        string $departmentId,
        string $session,
        string $semester,
    ): Collection {
        return $this->service->getCourseRegistrationsByDepartmentSessionAndSemester($departmentId, $session, $semester);
    }

    /** @return \Illuminate\Support\Collection<int, \App\Data\Download\PortalCourseRegistrationData> */
    public function getCourseRegistrationsBySessionAndCourse(string $session, string $courseId): Collection
    {
        return $this->service->getCourseRegistrationsBySessionAndCourse($session, $courseId);
    }

    /**
     * @param \Illuminate\Support\Collection<int, \App\Data\Download\PortalCourseRegistrationData> $registrations
     * @return \Illuminate\Support\Collection<int, \App\Data\Response\ResponseData>
     */
    public function saveCourseRegistrations(Collection $registrations): Collection
    {
        $results = [];

        foreach ($registrations as $registration) {
            try {
                $this->saveCourseRegistration($registration);
                $results[] = ResponseData::from($registration->registrationNumber, true);
            } catch (Exception $e) {
                $results[] = ResponseData::from($registration->registrationNumber, $e->getMessage());

                continue;
            }
        }

        return ResponseData::collect(collect($results));
    }

    /** @throws \Exception */
    public function saveCourseRegistration(PortalCourseRegistrationData $courseRegistrationData): CourseRegistration
    {
        $pendingRegistration = PendingCourseRegistration::new($courseRegistrationData);

        $pendingRegistration->save();

        return $pendingRegistration->registration;
    }
}
