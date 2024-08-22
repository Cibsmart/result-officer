<?php

declare(strict_types=1);

return [
    'base_url' => env('RP_HTTP_BASE_URL', 'https://portal.ebsu.edu.ng/api'),
    'endpoints' => [
        'courses' => env('RP_HTTP_ENDPOINT_COURSE', 'course.ashx'),
        'course_registrations' => env('RP_HTTP_ENDPOINT_COURSE_REGISTRATION', 'course-registration.ashx'),
        'departments' => env('RP_HTTP_ENDPOINT_DEPARTMENT', 'departments.ashx'),
        'results' => env('RP_HTTP_ENDPOINT_RESULT', 'results.ashx'),
        'students' => env('RP_HTTP_ENDPOINT_STUDENT', 'students.ashx'),
    ],
];
