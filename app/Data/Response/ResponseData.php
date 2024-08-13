<?php

declare(strict_types=1);

namespace App\Data\Response;

use Spatie\LaravelData\Data;

final class ResponseData extends Data
{
    public function __construct(
        public readonly string $key,
        public readonly string|true $message,
    ) {
    }

    /** @param array{string, string|true} $data */
    public static function fromArray(array $data): self
    {
        return new self($data[0], $data[1]);
    }
}
