<?php

declare(strict_types=1);

namespace App\Http\Controllers\Download\Results;

use App\Data\Course\CourseListData;
use App\Data\Department\DepartmentListData;
use App\Data\Level\LevelListData;
use App\Data\Semester\SemesterListData;
use App\Data\Session\SessionListData;
use App\ViewModels\Downloads\DownloadCourseRegistrationPage;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

final class DownloadResultsPageController
{
    public function __invoke(Request $request): Response
    {
        $searchTerm = $request->string('search')->value();

        return Inertia::render('download/results/page', new DownloadCourseRegistrationPage(
            departments: DepartmentListData::new(),
            sessions: SessionListData::new(),
            semesters: SemesterListData::new(),
            courses: fn () => CourseListData::new($searchTerm),
            levels: LevelListData::new(),
        ));
    }
}
