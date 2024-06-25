<?php

namespace Database\Seeders;

use App\Models\Semester;
use Illuminate\Database\Seeder;

class SemesterSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
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
