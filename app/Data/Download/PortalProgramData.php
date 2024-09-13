<?php

declare(strict_types=1);

namespace App\Data\Download;

use Spatie\LaravelData\Data;

final class PortalProgramData extends Data
{
    public function __construct(
        public readonly string $name,
    ) {
    }

    public static function fromString(string $data): self
    {
        return new self(name: $data);
    }
}
