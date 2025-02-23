<?php

declare(strict_types=1);

namespace App\Data\States;

use App\Models\State;
use Illuminate\Support\Collection;
use Spatie\LaravelData\Data;

final class StateListData extends Data
{
    public function __construct(
        /** @var \Illuminate\Support\Collection<int, \App\Data\States\StateData> $data */
        public readonly Collection $data,
    ) {
    }

    public static function new(): self
    {
        $default = new StateData(id: 0, name: 'Select State', localGovernments: null);

        return new self(
            data: StateData::collect(
                State::query()
                    ->orderBy('name')
                    ->get(),
            )->prepend($default),
        );
    }
}
