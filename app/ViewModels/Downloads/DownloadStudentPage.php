<?php

declare(strict_types=1);

namespace App\ViewModels\Downloads;

use App\Data\Department\DepartmentListData;
use App\Data\Session\SessionListData;
use Spatie\LaravelData\Data;

final class DownloadStudentPage extends Data
{
    public function __construct(
        public readonly DepartmentListData $department,
        public readonly SessionListData $session,
    ) {
    }
}
