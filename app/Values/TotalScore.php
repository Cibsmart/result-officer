<?php

declare(strict_types=1);

namespace App\Values;

use App\Enums\Grade;
use InvalidArgumentException;

final readonly class TotalScore
{
    public function __construct(
        public int $value,
    ) {
        if (! self::isValid($value)) {
            throw new InvalidArgumentException('Total score value must be between 0 and 100');
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

    public static function isValid(int $value): bool
    {
        return $value >= 0 && $value <= 100;
    }

    public function grade(bool $isEGradeAllowed): Grade
    {
        return Grade::for($this, $isEGradeAllowed);
    }
}
