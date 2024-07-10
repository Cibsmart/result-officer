<?php

declare(strict_types=1);

namespace App\Values;

use InvalidArgumentException;

final readonly class TotalScore
{
    public function __construct(
        public int $value,
    ) {
        if ($this->value < 0 || $this->value > 100) {
            throw new InvalidArgumentException('Score must be between 0 and 100');
        }
    }

    public static function fromInCourseAndExam(InCourseScore $inCourseScore, ExamScore $examScore): self
    {
        return new self($inCourseScore->value + $examScore->value);
    }

    public static function new(int $value): self
    {
        return new self($value);
    }
}
