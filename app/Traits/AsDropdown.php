<?php

declare(strict_types=1);

namespace App\Traits;

use App\Data\Dropdown\DropdownData;
use Illuminate\Support\Collection;

trait AsDropdown
{
    /** @return \Illuminate\Support\Collection<int, \App\Data\Dropdown\DropdownData> */
    public static function dropdown(): Collection
    {
        return DropdownData::collect(collect(self::cases()));
    }
}
