<?php

declare(strict_types=1);

namespace App\Http\Controllers\Download\Courses;

use App\Data\Import\ImportEventData;
use App\Data\Import\PendingImportEventData;
use App\Models\User;
use App\ViewModels\Downloads\DownloadCoursesPage;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

final readonly class DownloadCoursesPageController
{
    public function __invoke(Request $request): Response
    {
        $user = $request->user();

        assert($user instanceof User);

        return Inertia::render('download/courses/page', new DownloadCoursesPage(
            events: fn () => ImportEventData::new($user),
            pending: fn () => PendingImportEventData::new($user)),
        );
    }
}
