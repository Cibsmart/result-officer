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
        [$message, $author] = str(Inspiring::quotes()->random())->explode('-');

        return new self(message: $message, author: $author);
    }
}
