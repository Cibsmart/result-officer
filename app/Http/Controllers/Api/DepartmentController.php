<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Data\Department\DepartmentListData;

final class DepartmentController
{
    public function __invoke(): DepartmentListData
    {
        return DepartmentListData::new();
    }
}
