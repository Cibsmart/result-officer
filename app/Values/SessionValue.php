<?php

declare(strict_types=1);

namespace App\Values;

use Illuminate\Support\Str;
use InvalidArgumentException;

final readonly class SessionValue
{
    public function __construct(public string $value)
    {
        if (! preg_match('/^\d{4}-\d{4}$/i', Str::trim($this->value))) {
            throw new InvalidArgumentException('Invalid session');
        }
    }

    public static function new(string $session): self
    {
        return new self($session);
    }

    public function firstYear(): int
    {
        return (int) Str::of($this->value)->explode('-')->first();
    }

    public function lastYear(): int
    {
        return (int) Str::of($this->value)->explode('-')->last();
    }
}
