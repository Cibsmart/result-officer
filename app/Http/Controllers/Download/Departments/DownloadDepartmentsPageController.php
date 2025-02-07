<?php

declare(strict_types=1);

namespace App\Http\Controllers\Download\Departments;

use App\Data\Imports\ImportEventData;
use App\Data\Imports\PendingImportEventData;
use App\Enums\ImportEventType;
use App\ViewModels\Downloads\DownloadCoursesPage;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

final readonly class DownloadDepartmentsPageController
{
    public function __invoke(Request $request): Response
    {
        $user = $request->user();

        return Inertia::render('download/departments/page', new DownloadCoursesPage(
            events: fn () => ImportEventData::new($user, ImportEventType::DEPARTMENTS),
            pending: fn () => PendingImportEventData::new($user, ImportEventType::DEPARTMENTS)),
        );
    }
}
