<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Session;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

final class SessionSeeder extends Seeder
{
    public function run(): void
    {
        for ($year = 1994; $year <= 2025; $year ++) {
            $next = $year + 1;
            Session::query()->create([
                'name' => "$year/$next",
                'slug' => Str::slug("$year-$next"),
            ]);
        }
    }
}
