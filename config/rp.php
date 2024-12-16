<?php

declare(strict_types=1);

return [

    'chromium' => [
        'path' => env('RP_CHROMIUM_PATH'),
        'temp' => env('RP_CHROMIUM_TEMP', '/home/www-data'),
    ],

    'domain' => env('RP_DOMAIN', 'ebsu.edu.ng'),

    'http' => [
        'base_url' => env('RP_HTTP_BASE_URL', 'https://portal.ebsu.edu.ng/api'),
        'endpoints' => [
            'courses' => env('RP_HTTP_ENDPOINT_COURSE', 'course.ashx'),
            'departments' => env('RP_HTTP_ENDPOINT_DEPARTMENT', 'departments.ashx'),
            'registrations' => env('RP_HTTP_ENDPOINT_REGISTRATION', 'course-registration.ashx'),
            'results' => env('RP_HTTP_ENDPOINT_RESULT', 'results.ashx'),
            'students' => env('RP_HTTP_ENDPOINT_STUDENT', 'students.ashx'),
        ],
    ],
];
