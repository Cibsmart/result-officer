<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Enrollment;
use Illuminate\Database\Seeder;

final class EnrollmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Enrollment::query()->create([
            'level_id' => 1,
            'session_id' => 1,
            'student_id' => 1,
        ]);

        Enrollment::query()->create([
            'level_id' => 2,
            'session_id' => 2,
            'student_id' => 1,
        ]);

        Enrollment::query()->create([
            'level_id' => 3,
            'session_id' => 3,
            'student_id' => 1,
        ]);

        Enrollment::query()->create([
            'level_id' => 4,
            'session_id' => 4,
            'student_id' => 1,
        ]);

        $this->call([
            ResultSeeder::class,
        ]);
    }
}
