<?php

declare(strict_types=1);

namespace App\Enums;

enum VettingStatus: string
{
    case NEW = 'new';
    case CHECKING = 'checking';
    case UNCHECKED = 'unchecked';
    case FAILED = 'failed';
    case PASSED = 'passed';

    public function color(): StatusColor
    {
        return match ($this) {
            self::NEW => StatusColor::GRAY,
            self::UNCHECKED => StatusColor::YELLOW,
            self::CHECKING => StatusColor::INDIGO,
            self::FAILED => StatusColor::RED,
            self::PASSED => StatusColor::GREEN,
        };
    }
}
