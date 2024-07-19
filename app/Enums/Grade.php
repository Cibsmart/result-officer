<?php

declare(strict_types=1);

namespace App\Enums;

enum Grade: int
{
    case A = 5;
    case B = 4;
    case C = 3;
    case D = 2;
    case E = 1;
    case F = 0;

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
}
