<?php

declare(strict_types=1);

namespace App\Enums;

enum StudentStatus: string
{
    case NEW = 'new';
    case ACTIVE = 'active';
    case INACTIVE = 'inactive';
    case PROBATION = 'probation';
    case WITHDRAWN = 'withdrawn';
    case EXPELLED = 'expelled';
    case SUSPENDED = 'suspended';
    case DECEASED = 'deceased';
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
}
