<?php

declare(strict_types=1);

namespace App\Enums;

use App\Models\Level;

enum ProgramDuration: int
{
    case THREE = 3;
    case FOUR = 4;
    case FIVE = 5;
    case SIX = 6;

    public function level(): ?Level
    {
        $levelName = match ($this) {
            self::THREE, self::FOUR => LevelEnum::LEVEL_400,
            self::FIVE => LevelEnum::LEVEL_500,
            self::SIX => LevelEnum::LEVEL_600,
        };

        return Level::query()->where('name', $levelName)->first();
    }
}
