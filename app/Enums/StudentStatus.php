<?php

declare(strict_types=1);

namespace App\Enums;

use App\Traits\AsDropdown;

enum StudentStatus: string
{
    use AsDropdown;

    case NEW = 'new';
    case ACTIVE = 'active';
    case INACTIVE = 'inactive';
    case PROBATION = 'probation';
    case WITHDRAWN = 'withdrawn';
    case EXPELLED = 'expelled';
    case SUSPENDED = 'suspended';
    case DECEASED = 'deceased';
    case UNKNOWN = 'unknown';
    case TRANSFERRED = 'transferred';
    case FINAL_YEAR = 'final';
    case EXTRA_YEAR = 'extra';
    case CLEARED = 'cleared';
    case GRADUATED = 'graduated';

    /** @return array<int, \App\Enums\StudentStatus> */
    public static function vettableStates(): array
    {
        return [self::FINAL_YEAR, self::EXTRA_YEAR];
    }

    /** @return array<int, \App\Enums\StudentStatus> */
    public static function archivedStates(): array
    {
        return [self::CLEARED, self::GRADUATED];
    }

    public static function canBeCleared(self $status): bool
    {
        return in_array($status, self::vettableStates(), true);
    }

    public function color(): StatusColor
    {
        return match ($this) {
            self::NEW, self::UNKNOWN, self::INACTIVE, self::WITHDRAWN => StatusColor::GRAY,
            self::ACTIVE, self::FINAL_YEAR, => StatusColor::BLUE,
            self::EXTRA_YEAR => StatusColor::INDIGO,
            self::EXPELLED, self::DECEASED => StatusColor::RED,
            self::CLEARED, self::GRADUATED => StatusColor::GREEN,
            self::PROBATION, self::SUSPENDED => StatusColor::YELLOW,
            self::TRANSFERRED => StatusColor::PINK,
        };
    }
}
