<?php

declare(strict_types=1);

namespace App\ViewModels\Downloads;

use App\Data\Course\CourseListData;
use App\Data\Department\DepartmentListData;
use App\Data\Level\LevelListData;
use App\Data\Semester\SemesterListData;
use App\Data\Session\SessionListData;
use Spatie\LaravelData\Data;

final class DownloadCourseRegistrationPage extends Data
{
    public function __construct(
        public readonly DepartmentListData $departments,
        public readonly SessionListData $sessions,
        public readonly SemesterListData $semesters,
        public readonly CourseListData $courses,
        public readonly LevelListData $levels,
    ) {
    }
}
