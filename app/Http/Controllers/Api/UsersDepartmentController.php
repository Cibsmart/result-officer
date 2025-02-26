<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Data\Department\DepartmentListData;
use App\Models\User;

final class UsersDepartmentController
{
    public function __invoke(): DepartmentListData
    {
        $user = auth()->user();
        assert($user instanceof User);

        return DepartmentListData::forUser($user);
    }
}
