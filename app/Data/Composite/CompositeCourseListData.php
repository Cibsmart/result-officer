<?php

declare(strict_types=1);

namespace App\Data\Composite;

use Spatie\LaravelData\Data;

final class CompositeCourseListData extends Data
{
    public function __construct(
        public readonly string $code,
        public readonly int $unit,
    ) {
    }

    /** @param array{code: string, unit: int} $data */
    public static function fromArray(array $data): self
    {
        return new self(code: $data['code'], unit: $data['unit']);
    }
}
