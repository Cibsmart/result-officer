<?php

declare(strict_types=1);

namespace App\Data\Clearance;

use Spatie\LaravelData\Data;

final class ClearanceMonthData extends Data
{
    public function __construct(
        public readonly int $id,
        public readonly string $name,
    ) {
    }

    /** @param array<int, \App\Enums\Months> $month */
    public static function fromArray(array $month): self
    {
        $key = key($month);
        assert($key !== null);

        return new self(id: $key, name: $month[$key]->value);
    }
}
