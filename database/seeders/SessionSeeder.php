<?php

namespace Database\Seeders;

use App\Models\Session;
use Illuminate\Database\Seeder;

class SessionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        for ($year = 2017; $year < 2024; $year++) {
            $next = $year + 1;
            Session::query()->create([
                'name' => "$year/$next",
            ]);
        }
    }
}
