<?php

declare(strict_types=1);

namespace App\Data\States;

use App\Models\State;
use Spatie\LaravelData\Data;

final class StateData extends Data
{
    public function __construct(
        public readonly int $id,
        public readonly string $name,
        public readonly ?LocalGovernmentListData $localGovernments,
    ) {
    }

    public static function fromModel(State $state): self
    {
        $localGovernments = LocalGovernmentListData::forState($state);

        return new self(id: $state->id, name: $state->name, localGovernments: $localGovernments);
    }
}
