<?php

namespace App\Data\Clearance;

use Spatie\LaravelData\Data;

class ClearanceYearData extends Data
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
        return new self(id: $key, name: $year[$key]);
    }
}
