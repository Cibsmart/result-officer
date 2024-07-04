<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\CreditUnit;
use Illuminate\Database\Seeder;

final class CreditUnitSeeder extends Seeder
{
    public function run(): void
    {
        foreach ([1, 2, 3, 6, 12, 15, 18] as $unit) {
            CreditUnit::query()->create([
                'value' => $unit,
            ]);
        }
    }
}
