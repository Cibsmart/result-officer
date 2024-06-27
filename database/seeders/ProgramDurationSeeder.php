<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\ProgramDuration;
use Illuminate\Database\Seeder;

final class ProgramDurationSeeder extends Seeder
{

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        ProgramDuration::query()->create([
            'entry_mode_id' => 1,
            'program_id' => 1,
            'value' => 4,
        ]);

        ProgramDuration::query()->create([
            'entry_mode_id' => 2,
            'program_id' => 1,
            'value' => 3,
        ]);
    }

}
