<?php

declare(strict_types=1);

namespace App\Enums;

enum CreditUnit: int
{
    case ZERO = 0;
    case ONE = 1;
    case TWO = 2;
    case THREE = 3;
    case FOUR = 4;
    case FIVE = 5;
    case SIX = 6;
    case SEVEN = 7;
    case EIGHT = 8;
    case NINE = 9;
    case TEN = 10;
    case ELEVEN = 11;
    case TWELVE = 12;
    case FIFTY = 15;
    case EIGHTEEN = 18;
    case TWENTYFOUR = 24;

    public function getLabel(): ?string
    {
        return $this->name;
    }
}
