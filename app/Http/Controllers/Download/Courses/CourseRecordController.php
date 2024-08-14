<?php

declare(strict_types=1);

namespace App\Http\Controllers\Download\Courses;

use App\Repositories\CourseRepository;

final readonly class CourseRecordController
{
    public function __construct(private CourseRepository $repository)
    {
    }

    /** @throws \Exception */
    public function getAndSaveCourses(): void
    {
        $courses = $this->repository->getCourses();

        $this->repository->saveCourses($courses);

        dd($courses);
    }

    /** @throws \Exception */
    public function getAndSaveCourse(string $courseId): void
    {
        $course = $this->repository->getCourse($courseId);

        $this->repository->saveCourse($course);

        dd($course);
    }
}
