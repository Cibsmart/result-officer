<?php

namespace Database\Seeders;

use App\Models\CourseStatus;
use Illuminate\Database\Seeder;

class CourseStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        CourseStatus::query()->create([
            'code' => 'F',
            'name' => 'FRESH',
        ]);

        CourseStatus::query()->create([
            'code' => 'R',
            'name' => 'REPEAT',
        ]);
    }
}
