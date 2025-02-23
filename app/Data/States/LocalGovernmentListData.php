<?php

declare(strict_types=1);

namespace App\Data\States;

use App\Models\LocalGovernment;
use App\Models\State;
use Illuminate\Support\Collection;
use Spatie\LaravelData\Data;

final class LocalGovernmentListData extends Data
{
    public function __construct(
        /** @var \Illuminate\Support\Collection<int, \App\Data\States\LocalGovernmentData> $data */
        public readonly Collection $data,
    ) {
    }

    public static function forState(State $state): self
    {
        $default = new LocalGovernmentData(id: 0, name: 'Select Local Government');

        return new self(
            data: LocalGovernmentData::collect(
                LocalGovernment::query()
                    ->where('state_id', $state->id)
                    ->orderBy('name')
                    ->get())
                ->prepend($default)
                ->values(),
        );
    }
}
