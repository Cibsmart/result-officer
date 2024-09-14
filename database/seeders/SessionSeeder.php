<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Session;
use Illuminate\Database\Seeder;

final class SessionSeeder extends Seeder
{
    public function run(): void
    {
        for ($year = 2009; $year <= 2024; $year ++) {
            $next = $year + 1;
            Session::query()->create([
                'name' => "$year-$next",
            ]);
        }
    }
}
