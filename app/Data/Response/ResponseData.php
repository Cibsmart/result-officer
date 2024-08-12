<?php

declare(strict_types=1);

namespace App\Data\Response;

use Spatie\LaravelData\Data;

final class ResponseData extends Data
{
    public function __construct(
        public readonly string $key,
        public readonly string $message,
    ) {
    }

    public static function fromValue(string $key, string $message): self
    {
        return new self($key, $message);
    }
}
