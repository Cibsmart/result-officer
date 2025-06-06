<?php

declare(strict_types=1);

namespace App\Values;

use InvalidArgumentException;

final readonly class InCourseScore
{
    public function __construct(public int $value)
    {
        if ($value < 0 || $value > 50) {
            throw new InvalidArgumentException('In-course score value must be between 0 and 50');
        }
    }

    public static function new(int $value): self
    {
        return new self($value);
    }
}
