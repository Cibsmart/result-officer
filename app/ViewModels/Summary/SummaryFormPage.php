<?php

declare(strict_types=1);

namespace App\ViewModels\Summary;

use App\Data\Department\DepartmentListData;
use App\Data\Level\LevelListData;
use App\Data\Session\SessionListData;
use Spatie\LaravelData\Data;

final class SummaryFormPage extends Data
{
    public function __construct(
        public readonly DepartmentListData $department,
        public readonly SessionListData $session,
        public readonly LevelListData $level,
    ) {
    }
}
