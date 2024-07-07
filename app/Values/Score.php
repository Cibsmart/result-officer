<?php

declare(strict_types=1);

namespace App\Values;

final readonly class Score
{
    public function __construct(
        private InCourseScore $inCourseScore,
        private ExamScore $examScore,
    ) {
    }

    public static function new(InCourseScore $inCourseScore, ExamScore $examScore): self
    {
        return new self($inCourseScore, $examScore);
    }

    public function value(): int
    {
        return $this->inCourseScore->value + $this->examScore->value;
    }
}
