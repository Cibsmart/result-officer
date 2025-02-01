<?php

namespace App\Http\Controllers\Exports\Results;

use App\Data\Course\CourseListData;
use App\Data\Department\DepartmentListData;
use App\Data\Import\ImportEventData;
use App\Data\Import\PendingImportEventData;
use App\Data\Level\LevelListData;
use App\Data\Semester\SemesterListData;
use App\Data\Session\SessionListData;
use App\Enums\ImportEventType;
use App\Models\User;
use App\ViewModels\Downloads\DownloadRegistrationPage;
use App\ViewModels\Exports\ExportResultsPage;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ExportResultsPageController
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        $selectedIndex = $request->integer('selectedIndex');

        $user = $request->user();
        assert($user instanceof User);

        return Inertia::render('export/results/page', new ExportResultsPage(
            departments: DepartmentListData::forUser($user),
            sessions: SessionListData::new(),
            selectedIndex: $selectedIndex),
        );
    }
}
