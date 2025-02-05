<?php

declare(strict_types=1);

namespace App\Enums;

enum Months: string
{
    case JANUARY = 'January';
    case FEBRUARY = 'February';
    case MARCH = 'March';
    case APRIL = 'April';
    case MAY = 'May';
    case JUNE = 'June';
    case JULY = 'July';
    case AUGUST = 'August';
    case SEPTEMBER = 'September';
    case OCTOBER = 'October';
    case NOVEMBER = 'November';
    case DECEMBER = 'December';

    public static function all(): array
    {
        return [
            [1 => self::JANUARY],
            [2 => self::FEBRUARY],
            [3 => self::MARCH],
            [4 => self::APRIL],
            [5 => self::MAY],
            [6 => self::JUNE],
            [7 => self::JULY],
            [8 => self::AUGUST],
            [9 => self::SEPTEMBER],
            [10 => self::OCTOBER],
            [11 => self::NOVEMBER],
            [12 => self::DECEMBER],
        ];
    }
}
