<?php

declare(strict_types=1);

namespace App\Values;

use Illuminate\Support\Str;
use InvalidArgumentException;

final readonly class RegistrationNumber
{
    public function __construct(public string $value)
    {
        if (! preg_match('/^EBSU\/\d{4}\/\d{4,6}[A-Z]?$/i', Str::trim($this->value))) {
            throw new InvalidArgumentException('Invalid registration number');
        }
    }

    public static function new(string $value): self
    {
        return new self($value);
    }
}
