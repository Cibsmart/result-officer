<?php

namespace App\Values;

use InvalidArgumentException;

final readonly class InCourseScore
{
    public function __construct(private int $value)
    {
        if ($value < 0 || $value > 30) {
            throw new InvalidArgumentException('In-course score value must be between 0 and 30');
        }
    }
}
