<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Session;
use Illuminate\Database\Seeder;

final class SessionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        for ($year = 2014; $year < 2024; $year++) {
            $next = $year + 1;
            Session::query()->create([
                'name' => "$year/$next",
            ]);
        }
    }
}
