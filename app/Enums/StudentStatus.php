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
    case VETTING = 'vetting';
    case VETTED = 'vetted';
    case CLEARED = 'cleared';
    case GRADUATED = 'graduated';

    /** @return array<int, \App\Enums\StudentStatus> */
    public static function vettableStates(): array
    {
        return [self::FINAL_YEAR, self::EXTRA_YEAR, self::VETTING, self::VETTED];
    }

    /** @return array<int, \App\Enums\StudentStatus> */
    public static function clearableStates(): array
    {
        return [self::VETTED];
    }
}
