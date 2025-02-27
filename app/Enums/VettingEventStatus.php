<?php

declare(strict_types=1);

namespace App\Enums;

enum VettingEventStatus: string
{
    case NEW = 'new';
    case PENDING = 'pending';
    case QUEUED = 'queued';
    case VETTING = 'vetting';
    case FAILED = 'failed';
    case PASSED = 'passed';
    case COMPLETED = 'completed';

    public static function passed(self $status): bool
    {
        return $status === self::PASSED;
    }

    public function color(): StatusColor
    {
        return match ($this) {
            self::NEW, self::PENDING => StatusColor::GRAY,
            self::QUEUED => StatusColor::YELLOW,
            self::VETTING, => StatusColor::PURPLE,
            self::FAILED => StatusColor::RED,
            self::COMPLETED => StatusColor::BLUE,
            self::PASSED => StatusColor::GREEN,
        };
    }
}
