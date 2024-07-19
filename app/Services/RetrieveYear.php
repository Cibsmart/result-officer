<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Support\Str;

final readonly class RetrieveYear
{
    public function __construct(public string $session)
    {
    }

    public static function fromSession(string $session): self
    {
        return new self($session);
    }

    public function firstYear(): int
    {
        return (int) Str::of($this->session)->explode('/')->first();
    }

    public function lastYear(): int
    {
        return (int) Str::of($this->session)->explode('/')->last();
    }
}
