<?php

declare(strict_types=1);

namespace App\Data\Clearance;

use App\Enums\Months;
use Illuminate\Support\Collection;
use Spatie\LaravelData\Data;

final class ClearanceMonthListData extends Data
{
    public function __construct(
        /** @var \Illuminate\Support\Collection<int, \App\Data\Clearance\ClearanceMonthData> */
        public readonly Collection $months,
    ) {
    }

    public static function new(): self
    {
        $default = new ClearanceMonthData(id: 0, name: 'Select Batch Month');

        return new self(
            months: ClearanceMonthData::collect(collect(Months::all()))->prepend($default),
        );
    }
}
