<?php

declare(strict_types=1);

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum Role: string implements HasLabel
{
    case SUPER_ADMIN = 'super-admin';
    case ADMIN = 'admin';
    case DESK_OFFICER = 'desk-officer';
    case EXAM_OFFICER = 'exam-officer';
    case DATABASE_OFFICER = 'database-officer';
    case USER = 'user';

    /**
     * Derived from getLabel() rather than restated. The two were maintained
     * separately and drifted: this array mapped desk-officer to 'EXAM OFFICER'
     * and exam-officer to 'DESK OFFICER', so every account created through the
     * role dropdown was persisted one step from what the operator picked.
     * @return array<string, string>
     */
    public static function creatable(): array
    {
        $creatable = [];

        foreach (self::cases() as $role) {
            if ($role === self::SUPER_ADMIN) {
                continue;
            }

            $creatable[$role->value] = $role->getLabel();
        }

        return $creatable;
    }

    public function getLabel(): string
    {
        return match ($this) {
            self::SUPER_ADMIN => 'SUPER ADMIN',
            self::ADMIN => 'ADMIN',
            self::DESK_OFFICER => 'DESK OFFICER',
            self::EXAM_OFFICER => 'EXAM OFFICER',
            self::DATABASE_OFFICER => 'DATABASE OFFICER',
            self::USER => 'USER',
        };
    }
}
