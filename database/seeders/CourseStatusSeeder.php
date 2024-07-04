<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\CourseStatus;
use Illuminate\Database\Seeder;

final class CourseStatusSeeder extends Seeder
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
