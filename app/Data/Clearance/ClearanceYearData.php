<?php

declare(strict_types=1);

namespace App\Data\Clearance;

use Spatie\LaravelData\Data;

final class ClearanceYearData extends Data
{
    public function __construct(
        public readonly int $id,
        public readonly string $name,
    ) {
    }

    /** @param array<int, string> $year */
    public static function fromArray(array $year): self
    {
        $key = key($year);
        assert($key !== null);

        return new self(id: $key, name: $year[$key]);
    }
}
