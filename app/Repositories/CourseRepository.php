<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Data\Download\PortalCourseData;
use App\Data\Response\ResponseData;
use App\Models\Course;
use App\Services\Api\CourseService;
use Exception;
use Illuminate\Support\Collection;

final class CourseRepository
{
    public function __construct(public CourseService $service)
    {
    }

    /**
     * @return \Illuminate\Support\Collection<int, \App\Data\Download\PortalCourseData>
     * @throws \Exception
     */
    public function getCourses(): Collection
    {
        return $this->service->getAllCourses();
    }

    /** @throws \Exception */
    public function getCourse(string $courseId): PortalCourseData
    {
        $course = $this->service->getCourseDetail($courseId)->first();

        assert($course instanceof PortalCourseData);

        return $course;
    }

    /**
     * @param \Illuminate\Support\Collection<int, \App\Data\Download\PortalCourseData> $courses
     * @return \Illuminate\Support\Collection<int, \App\Data\Response\ResponseData>
     */
    public function saveCourses(Collection $courses): Collection
    {
        $results = [];

        foreach ($courses as $course) {
            try {
                $this->saveCourse($course);
                $results[] = ResponseData::from([$course->code, true]);
            } catch (Exception $e) {
                $results[] = ResponseData::from([$course->code, $e->getMessage()]);

                continue;
            }
        }

        return collect($results);
    }

    public function saveCourse(PortalCourseData $course): Course
    {
        return Course::firstOrCreate(
            ['code' => $course->code, 'title' => $course->title],
            ['online_id' => $course->onlineId, 'active' => 1],
        );
    }
}
