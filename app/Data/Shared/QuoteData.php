<?php

declare(strict_types=1);

namespace App\Data\Shared;

use Illuminate\Foundation\Inspiring;
use Spatie\LaravelData\Data;

final class QuoteData extends Data
{
    public function __construct(
        public readonly string $message,
        public readonly string $author,
    ) {
    }

    public static function new(): self
    {
        $parts = str(Inspiring::quotes()->random())->explode('-');

        return new self(message: (string) $parts->get(0), author: (string) $parts->get(1));
    }
}
