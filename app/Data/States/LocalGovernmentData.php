<?php

declare(strict_types=1);

namespace App\Data\States;

use Spatie\LaravelData\Data;

final class LocalGovernmentData extends Data
{
    public function __construct(
        public readonly int $id,
        public readonly string $name,
    ) {
    }

    public static function fromModel(self $localGovernment): self
    {
        return new self(id: $localGovernment->id, name: $localGovernment->name);
    }
}
