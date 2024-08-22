<?php

declare(strict_types=1);

namespace App\Http\Clients;

final readonly class CourseClient extends ApiClient
{
    /**
     * @return array<int, array{id: string, course_code: string, course_title: string}>
     * @throws \Exception
     */
    public function fetchCourses(): array
    {
        /**
         * phpcs:ignore SlevomatCodingStandard.Files.LineLength
         * @var array<int, array{id: string, course_code: string, course_title: string}> $courses
         */
        $courses = $this->get('courses');

        return $courses;
    }

    /**
     * @return array<int, array{id: string, course_code: string, course_title: string}>
     * @throws \Exception
     */
    public function fetchCourseById(string $id): array
    {
        /** @var array<int, array{id: string, course_code: string, course_title: string}> $course */
        $course = $this->get("courses/$id");

        return $course;
    }
}
