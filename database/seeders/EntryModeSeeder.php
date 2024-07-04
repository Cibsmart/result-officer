<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\EntryMode;
use Illuminate\Database\Seeder;

final class EntryModeSeeder extends Seeder
{
    public function run(): void
    {
        EntryMode::query()->create([
            'code' => 'UTME',
            'name' => 'UNIFIED TERTIARY MATRICULATION EXAMINATION',
        ]);

        EntryMode::query()->create([
            'code' => 'DE',
            'name' => 'DIRECT ENTRY',
        ]);
    }
}
