<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\LevelEnum;
use App\Models\Level;
use Illuminate\Database\Seeder;

final class LevelSeeder extends Seeder
{
    public function run(): void
    {
        foreach (LevelEnum::cases() as $level) {
            Level::query()->create([
                'name' => $level->value,
            ]);
        }
    }
}
