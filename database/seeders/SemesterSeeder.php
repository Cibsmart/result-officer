<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Semester;
use Illuminate\Database\Seeder;

final class SemesterSeeder extends Seeder
{
    public function run(): void
    {
        Semester::query()->create([
            'name' => 'FIRST',
        ]);

        Semester::query()->create([
            'name' => 'SECOND',
        ]);
    }
}
