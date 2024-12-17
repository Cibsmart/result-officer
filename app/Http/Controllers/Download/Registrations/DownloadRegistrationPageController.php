<?php

declare(strict_types=1);

namespace App\Http\Controllers\Download\Registrations;

use App\Data\Course\CourseListData;
use App\Data\Department\DepartmentListData;
use App\Data\Import\ImportEventData;
use App\Data\Import\PendingImportEventData;
use App\Data\Level\LevelListData;
use App\Data\Semester\SemesterListData;
use App\Data\Session\SessionListData;
use App\Enums\ImportEventType;
use App\ViewModels\Downloads\DownloadRegistrationPage;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

final readonly class DownloadRegistrationPageController
{
    public function __invoke(Request $request): Response
    {
        $selectedIndex = $request->integer('selectedIndex');

        $user = $request->user();

        $searchTerm = $request->string('search')->value();

        return Inertia::render('download/registrations/page', new DownloadRegistrationPage(
            departments: DepartmentListData::forUser($user),
            sessions: SessionListData::new(),
            semesters: SemesterListData::new(),
            courses: fn () => CourseListData::new($searchTerm),
            levels: LevelListData::new(),
            events: fn () => ImportEventData::new($user, ImportEventType::REGISTRATIONS),
            pending: fn () => PendingImportEventData::new($user, ImportEventType::REGISTRATIONS),
            selectedIndex: $selectedIndex),
        );
    }
}
