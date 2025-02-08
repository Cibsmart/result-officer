<?php

declare(strict_types=1);

namespace App\Http\Controllers\Download\Courses;

use App\Data\Imports\ImportEventData;
use App\Data\Imports\PendingImportEventData;
use App\Enums\ImportEventType;
use App\ViewModels\Downloads\DownloadCoursesPage;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

final readonly class DownloadCoursesPageController
{
    public function __invoke(Request $request): Response
    {
        $user = $request->user();

        return Inertia::render('download/courses/page', new DownloadCoursesPage(
            events: fn () => ImportEventData::new($user, ImportEventType::COURSES),
            pending: fn () => PendingImportEventData::new($user, ImportEventType::COURSES)),
        );
    }
}
