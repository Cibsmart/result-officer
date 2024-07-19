<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\RecordsUnitHead;
use Illuminate\Database\Seeder;

final class RecordsUnitHeadSeeder extends Seeder
{
    public function run(): void
    {
        RecordsUnitHead::query()->create([
            'is_current' => true,
            'name' => 'AKPUKWA, E.S.',
        ]);
    }
}
