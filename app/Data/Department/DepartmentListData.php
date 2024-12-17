<?php

declare(strict_types=1);

namespace App\Data\Department;

use App\Models\Department;
use App\Models\User;
use App\Scopes\ActiveScope;
use Illuminate\Support\Collection;
use Spatie\LaravelData\Data;

final class DepartmentListData extends Data
{
    public function __construct(
        /** @var \Illuminate\Support\Collection<int, \App\Data\Department\DepartmentData> */
        public readonly Collection $data,
    ) {
    }

    public static function new(): self
    {
        $default = new DepartmentData(id: 0, name: 'Select Department', slug: '');

        return new self(
            data: DepartmentData::collect(
                Department::query()
                    ->tap(new ActiveScope())
                    ->orderBy('name')
                    ->get(),
            )->prepend($default),
        );
    }

    public static function forUser(User $user): self
    {
        $default = new DepartmentData(id: 0, name: 'Select Department', slug: '');

        $departmentIds = $user->departments()->pluck('department_id');

        return new self(
            data: DepartmentData::collect(
                Department::query()
                    ->when($user->isAdmin(), fn ($query) => $query->whereIn('id', $departmentIds))
                    ->tap(new ActiveScope())
                    ->orderBy('name')
                    ->get(),
            )->prepend($default),
        );
    }
}
