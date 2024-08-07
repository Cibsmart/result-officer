<?php

declare(strict_types=1);

namespace App\Data\Ingest;

use Spatie\LaravelData\Data;

final class PortalProgramData extends Data
{
    public function __construct(
        public readonly string $id,
        public readonly string $name,
    ) {
    }

    /** @param array{id: string, name: string} $data */
    public static function fromArray(array $data): self
    {
        return new self(id: $data['id'], name: $data['name']);
    }
}
