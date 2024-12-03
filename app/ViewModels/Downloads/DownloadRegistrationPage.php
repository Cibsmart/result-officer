<?php

declare(strict_types=1);

namespace App\ViewModels\Downloads;

use App\Data\Course\CourseListData;
use App\Data\Department\DepartmentListData;
use App\Data\Import\PendingImportEventData;
use App\Data\Level\LevelListData;
use App\Data\Semester\SemesterListData;
use App\Data\Session\SessionListData;
use Closure;
use Illuminate\Support\Collection;
use Spatie\LaravelData\Data;
use Spatie\TypeScriptTransformer\Attributes\TypeScriptType;

final class DownloadRegistrationPage extends Data
{
    public function __construct(
        public readonly DepartmentListData $departments,
        public readonly SessionListData $sessions,
        public readonly SemesterListData $semesters,
        #[TypeScriptType(CourseListData::class)]
        public readonly Closure $courses,
        public readonly LevelListData $levels,
        #[TypeScriptType(Collection::class)]
        public readonly Closure $events,
        #[TypeScriptType(PendingImportEventData::class)]
        public readonly Closure $pending,
        public readonly int $selectedIndex,
    ) {
    }
}
