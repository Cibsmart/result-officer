<?php

declare(strict_types=1);

namespace App\Http\Controllers\Download\Courses;

use App\Models\ImportEvent;
use Illuminate\Support\Facades\Artisan;

final class ContinueDownloadCoursesController
{
    public function __invoke(ImportEvent $event): void
    {
        Artisan::queue('courses:process', ['eventId' => $event->id]);
    }
}
