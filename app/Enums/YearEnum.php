<?php

declare(strict_types=1);

namespace App\Enums;

enum YearEnum: int
{
    case FIRST = 1;
    case SECOND = 2;
    case THIRD = 3;
    case FOURTH = 4;
    case FIFTH = 5;
    case SIXTH = 6;
    case SEVENTH = 7;
    case EIGHT = 8;
    case NINTH = 9;
    case TENTH = 10;
    case ELEVENTH = 11;
    case TWELFTH = 12;

    public function next(): self
    {
        return match ($this) {
            self::FIRST => self::SECOND,
            self::SECOND => self::THIRD,
            self::THIRD => self::FOURTH,
            self::FOURTH => self::FIFTH,
            self::FIFTH => self::SIXTH,
            self::SIXTH => self::SEVENTH,
            self::SEVENTH => self::EIGHT,
            self::EIGHT => self::NINTH,
            self::NINTH => self::TENTH,
            self::TENTH => self::ELEVENTH,
            self::ELEVENTH, self::TWENTH => self::TWENTH,
        };
    }
}
