<?php

declare(strict_types=1);

namespace App\Values;

use App\Models\Session;
use Illuminate\Support\Str;
use InvalidArgumentException;

final readonly class SessionValue
{
    public function __construct(public string $value)
    {
        if (! preg_match('/^(\d{4})\/(\d{4})$/i', Str::trim($this->value), $matches)) {
            throw new InvalidArgumentException('Invalid session');
        }

        if ((int) $matches[2] !== (int) $matches[1] + 1) {
            throw new InvalidArgumentException('Invalid session');
        }
    }

    public static function new(string $session): self
    {
        $session = Str::replace('-', '/', $session);

        return new self($session);
    }

    public function firstYear(): int
    {
        return (int) Str::of($this->value)->explode('/')->first();
    }

    public function lastYear(): int
    {
        return (int) Str::of($this->value)->explode('/')->last();
    }

    public function getSession(): Session
    {
        return Session::getUsingName($this->value);
    }
}
