<?php

declare(strict_types=1);

namespace App\Data\Department;

use App\Models\Department;
use App\Scopes\ActiveScope;
use Illuminate\Support\Collection;
use Spatie\LaravelData\Data;

final class DepartmentListData extends Data
{
    public function __construct(
        /** @var \Illuminate\Support\Collection<int, \App\Data\Department\DepartmentData> */
        public readonly Collection $departments,
    ) {
    }

    public static function new(): self
    {
        return new self(
            departments: DepartmentData::collect(
                Department::query()
                    ->tap(new ActiveScope())
                    ->get(),
            ),
        );
    }
}
