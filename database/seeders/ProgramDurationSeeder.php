<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\EntryMode;
use App\Models\ProgramDuration;
use Illuminate\Database\Seeder;

final class ProgramDurationSeeder extends Seeder
{
    public function run(): void
    {
        ProgramDuration::query()->create([
            'entry_mode' => EntryMode::UTME,
            'program_id' => 1,
            'value' => 4,
        ]);

        ProgramDuration::query()->create([
            'entry_mode' => EntryMode::DE,
            'program_id' => 1,
            'value' => 3,
        ]);
    }
}
