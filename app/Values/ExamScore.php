<?php

declare(strict_types=1);

namespace App\Values;

use InvalidArgumentException;

final readonly class ExamScore
{
    public function __construct(public int $value)
    {
        if ($value < 0 || $value > 70) {
            throw new InvalidArgumentException('Exam score value must be between 0 and 70');
        }
    }

    public static function new(int $value): self
    {
        return new self($value);
    }
}
