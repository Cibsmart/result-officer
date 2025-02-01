<?php

declare(strict_types=1);

namespace App\ViewModels\Exports;

use App\Data\Department\DepartmentListData;
use App\Data\Session\SessionListData;
use Spatie\LaravelData\Data;

final class ExportResultsPage extends Data
{
    public function __construct(
        public readonly DepartmentListData $departments,
        public readonly SessionListData $sessions,
        public readonly int $selectedIndex,
    ) {
    }
}
