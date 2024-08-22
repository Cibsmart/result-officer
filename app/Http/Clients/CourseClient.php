<?php

declare(strict_types=1);

namespace App\Http\Clients;

use Config;

final readonly class CourseClient extends ApiClient
{
    private string $endpoint;

    public function __construct()
    {
        $this->endpoint = Config::string('rp-http.endpoints.courses');
    }

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
        $courses = $this->get($this->endpoint);

        return $courses;
    }

    /**
     * @return array<int, array{id: string, course_code: string, course_title: string}>
     * @throws \Exception
     */
    public function fetchCourseById(string $id): array
    {
        /** @var array<int, array{id: string, course_code: string, course_title: string}> $course */
        $course = $this->get(endpoint: $this->endpoint, parameters: ['course_id' => $id]);

        return $course;
    }
}
