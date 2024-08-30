<?php

declare(strict_types=1);

namespace App\Data\Composite;

use Spatie\LaravelData\Data;

final class CompositeCourseData extends Data
{
    public function __construct(
        public readonly string $code,
        public readonly string $grade,
        public readonly string $score,
    ) {
    }

    /** @param array<string, string> $data */
    public static function fromArray(array $data): self
    {
        return new self(code: $data['code'], grade: $data['grade'], score: $data['score']);
    }
}
