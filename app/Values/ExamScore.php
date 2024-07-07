<?php

namespace App\Values;

use InvalidArgumentException;

final readonly class ExamScore
{
    public function __construct(public int $value)
    {
        if ($value < 0 || $value > 70) {
            throw new InvalidArgumentException('Value must be between 0 and 70');
        }
    }
}
