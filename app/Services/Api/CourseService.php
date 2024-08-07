<?php

declare(strict_types=1);

namespace App\Services\Api;

use App\Data\Ingest\PortalCourseData;
use App\Http\Clients\CourseClient;
use Illuminate\Support\Collection;

final class CourseService
{
    public function __construct(public CourseClient $client)
    {
    }

    /**
     * @return \Illuminate\Support\Collection<int, \App\Data\Ingest\PortalCourseData>
     * @throws \Exception
     */
    public function getAllCourses(): Collection
    {
        $courses = $this->client->fetchCourses();

        return PortalCourseData::collect(collect($courses));
    }

    /** @throws \Exception */
    public function getCourseDetail(string $onlineId): PortalCourseData
    {
        $course = $this->client->fetchCourseById($onlineId);

        return PortalCourseData::from($course);
    }
}
