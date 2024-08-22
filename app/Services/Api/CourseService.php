<?php

declare(strict_types=1);

namespace App\Services\Api;

use App\Data\Download\PortalCourseData;
use App\Http\Clients\CourseClient;
use Illuminate\Support\Collection;

final readonly class CourseService
{
    public function __construct(private CourseClient $client)
    {
    }

    /**
     * @return \Illuminate\Support\Collection<int, \App\Data\Download\PortalCourseData>
     * @throws \Exception
     */
    public function getAllCourses(): Collection
    {
        $courses = $this->client->fetchCourses();

        return PortalCourseData::collect(collect($courses));
    }

    /**
     * @return \Illuminate\Support\Collection<int, \App\Data\Download\PortalCourseData>
     * @throws \Exception
     */
    public function getCourseDetail(string $onlineId): Collection
    {
        $course = $this->client->fetchCourseById($onlineId);

        return PortalCourseData::collect(collect($course));
    }
}
