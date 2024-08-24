<?php

declare(strict_types=1);

namespace App\ViewModels\Reports;

use App\Data\Level\LevelListData;
use App\Data\Program\ProgramListData;
use App\Data\Semester\SemesterListData;
use App\Data\Session\SessionListData;
use Spatie\LaravelData\Data;

final class CompositeFormPage extends Data
{
    public function __construct(
        public readonly ProgramListData $program,
        public readonly SemesterListData $semester,
        public readonly SessionListData $session,
        public readonly LevelListData $level,
    ) {
    }
}
