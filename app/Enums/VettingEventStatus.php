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
    case QUEUED = 'queued';

    public static function passed(self $status): bool
    {
        return $status === self::PASSED;
    }

    public function color(): StatusColor
    {
        return match ($this) {
            self::NEW, self::PENDING => StatusColor::GRAY,
            self::QUEUED => StatusColor::PINK,
            self::VETTING, => StatusColor::BLUE,
            self::FAILED => StatusColor::RED,
            self::PASSED => StatusColor::GREEN,
        };
    }
}
