<?php

declare(strict_types=1);

namespace App\Enums;

enum VettingEventStatus: string
{
    case NEW = 'new';
    case PENDING = 'pending';
    case VETTING = 'vetting';
    case FAILED = 'failed';
    case PASSED = 'passed';

    public function color(): StatusColor
    {
        return match ($this) {
            self::NEW, self::PENDING => StatusColor::GRAY,
            self::VETTING, => StatusColor::INDIGO,
            self::FAILED => StatusColor::RED,
            self::PASSED => StatusColor::GREEN,
        };
    }
}
