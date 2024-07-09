<?php

declare(strict_types=1);

namespace App\Values;

use App\Enums\CreditUnitEnum;
use InvalidArgumentException;

final class CreditUnit
{
    public function __construct(public int $value)
    {
        if (is_null(CreditUnitEnum::tryFrom($this->value))) {
            throw new InvalidArgumentException('The credit unit is not a valid credit unit value');
        }
    }

    public static function new(int $value): self
    {
        return new self($value);
    }
}
