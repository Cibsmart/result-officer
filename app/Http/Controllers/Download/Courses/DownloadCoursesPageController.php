<?php

declare(strict_types=1);

namespace App\Http\Controllers\Download\Courses;

use Inertia\Inertia;
use Inertia\Response;

final readonly class DownloadCoursesPageController
{
    public function __invoke(): Response
    {
        return Inertia::render('download/courses/page');
    }
}
