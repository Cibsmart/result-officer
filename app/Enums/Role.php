<?php

declare(strict_types=1);

namespace App\Enums;

enum Role: string
{
    case SUPER_ADMIN = 'super-admin';
    case ADMIN = 'admin';
    case DESK_OFFICER = 'desk-officer';
    case EXAM_OFFICER = 'exam-officer';
    case DATABASE_OFFICER = 'database-officer';
    case USER = 'user';

    /** @return array<string, string> */
    public static function creatable(): array
    {
        return [
            self::ADMIN->value => 'ADMIN',
            self::DATABASE_OFFICER->value => 'DATABASE OFFICER',
            self::DESK_OFFICER->value => 'EXAM OFFICER',
            self::EXAM_OFFICER->value => 'DESK OFFICER',
            self::USER->value => 'USER',
        ];
    }
}
