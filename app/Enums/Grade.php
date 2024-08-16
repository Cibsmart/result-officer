<?php

declare(strict_types=1);

namespace App\Enums;

use App\Values\TotalScore;

enum Grade: string
{
    case A = 'A';
    case B = 'B';
    case C = 'C';
    case D = 'D';
    case E = 'E';
    case F = 'F';

    public static function for(TotalScore $score, bool $isEGradeAllowed = true): self
    {
        return match (true) {
            self::scoreWithinRangeOf(self::A, $score) => self::A,
            self::scoreWithinRangeOf(self::B, $score) => self::B,
            self::scoreWithinRangeOf(self::C, $score) => self::C,
            self::scoreWithinRangeOf(self::D, $score) => self::D,
            $isEGradeAllowed && self::scoreWithinRangeOf(self::E, $score) => self::E,
            default => self::F,
        };
    }

    public function min(): int
    {
        return match ($this) {
            self::A => 70,
            self::B => 60,
            self::C => 50,
            self::D => 45,
            self::E => 40,
            self::F => 0,
        };
    }

    public function max(): int
    {
        return match ($this) {
            self::A => 100,
            self::B => 69,
            self::C => 59,
            self::D => 49,
            self::E => 44,
            self::F => 39,
        };
    }

    public function interpretation(): string
    {
        return match ($this) {
            self::A => 'EXCELLENT',
            self::B => 'VERY GOOD',
            self::C => 'GOOD',
            self::D => 'FAIR',
            self::E => 'PASS',
            self::F => 'FAIL',
        };
    }

    public function point(): int
    {
        return match ($this) {
            self::A => 5,
            self::B => 4,
            self::C => 3,
            self::D => 2,
            self::E => 1,
            self::F => 0,
        };
    }

    private static function scoreWithinRangeOf(self $grade, TotalScore $score): bool
    {
        return $score->value >= $grade->min()
            && $score->value <= $grade->max();
    }
}
