<?php

declare(strict_types=1);

namespace App\ViewModels\Imports;

use App\Data\Department\DepartmentListData;
use App\Data\Imports\ExcelImportEventListData;
use Spatie\LaravelData\Data;

final class CurriculumImportPage extends Data
{
    public function __construct(
        public readonly ExcelImportEventListData $data,
        public readonly DepartmentListData $departments,
    ) {
    }
}
