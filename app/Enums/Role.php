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
    case USER = 'user';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::SUPER_ADMIN => 'SUPER ADMIN',
            self::ADMIN => 'ADMIN',
            self::DESK_OFFICER => 'DESK OFFICER',
            self::EXAM_OFFICER => 'EXAM OFFICER',
            self::USER => 'USER',
        };
    }

}
