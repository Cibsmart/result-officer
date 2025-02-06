<?php

declare(strict_types=1);

namespace App\Data\Clearance;

use Illuminate\Support\Collection;
use Spatie\LaravelData\Data;

final class ClearanceYearListData extends Data
{
    public function __construct(
        /** @var \Illuminate\Support\Collection<int, \App\Data\Clearance\ClearanceYearData> */
        public readonly Collection $years,
    ) {
    }

    public static function new(): self
    {
        $default = new ClearanceYearData(id: 0, name: 'Select Batch Year');

        $years = [
            [1 => (string) now()->subYear()->year],
            [2 => (string) now()->year],
            [3 => (string) now()->addYear()->year],
        ];

        return new self(
            years: ClearanceYearData::collect(collect($years))->prepend($default),
        );
    }
}
