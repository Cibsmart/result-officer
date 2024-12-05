<?php

declare(strict_types=1);

namespace App\Data\Level;

use App\Models\Level;
use Illuminate\Support\Collection;
use Spatie\LaravelData\Data;

final class LevelListData extends Data
{
    public function __construct(
        /** @var \Illuminate\Support\Collection<int, \App\Data\Level\LevelData> */
        public readonly Collection $levels,
    ) {
    }

    public static function new(): self
    {
        $default = new LevelData(id: 0, name: 'Select Level', slug: '');

        return new self(
            levels: LevelData::collect(
                Level::query()->orderBy('name')->get(),
            )->prepend($default),
        );
    }
}
