<?php

declare(strict_types=1);

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum GenderEnum: string implements HasLabel
{
    case MALE = 'M';
    case FEMALE = 'F';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::MALE => 'MALE',
            self::FEMALE => 'FEMALE',
        };
    }
}
