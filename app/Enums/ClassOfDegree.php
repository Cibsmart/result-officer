<?php

declare(strict_types=1);

namespace App\Enums;

enum ClassOfDegree: string
{
    case FIRST_CLASS = 'FIRST CLASS HONOURS';
    case SECOND_CLASS_UPPER = 'SECOND CLASS HONOURS (UPPER DIVISION)';
    case SECOND_CLASS_LOWER = 'SECOND CLASS HONOURS (LOWER DIVISION)';
    case THIRD_CLASS = 'THIRD CLASS HONOURS';
    case PASS = 'PASS';
    case FAIL = 'FAIL';

    public static function for(float $fcgpa): self
    {
        return match (true) {
            self::fcgpaWithinRangeOf(self::FIRST_CLASS, $fcgpa) => self::FIRST_CLASS,
            self::fcgpaWithinRangeOf(self::SECOND_CLASS_UPPER, $fcgpa) => self::SECOND_CLASS_UPPER,
            self::fcgpaWithinRangeOf(self::SECOND_CLASS_LOWER, $fcgpa) => self::SECOND_CLASS_LOWER,
            self::fcgpaWithinRangeOf(self::THIRD_CLASS, $fcgpa) => self::THIRD_CLASS,
            self::fcgpaWithinRangeOf(self::PASS, $fcgpa) => self::PASS,
            default => self::FAIL,
        };
    }

    public function min(): float
    {
        return match ($this) {
            self::FIRST_CLASS => 4.50,
            self::SECOND_CLASS_UPPER => 3.50,
            self::SECOND_CLASS_LOWER => 2.50,
            self::THIRD_CLASS => 1.50,
            self::PASS => 1.00,
            self::FAIL => 0.00,
        };
    }

    public function max(): float
    {
        return match ($this) {
            self::FIRST_CLASS => 5.00,
            self::SECOND_CLASS_UPPER => 4.49,
            self::SECOND_CLASS_LOWER => 3.49,
            self::THIRD_CLASS => 2.49,
            self::PASS => 1.49,
            self::FAIL => 0.99,
        };
    }

    private static function fcgpaWithinRangeOf(self $class, float $value): bool
    {
        return $value >= $class->min()
            && $value <= $class->max();
    }
}
