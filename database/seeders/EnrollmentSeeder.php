<?php

namespace Database\Seeders;

use App\Models\Enrollment;
use Illuminate\Database\Seeder;

class EnrollmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Enrollment::query()->create([
            'student_id' => 1,
            'session_id' => 1,
            'level_id' => 1,
        ]);

        Enrollment::query()->create([
            'student_id' => 1,
            'session_id' => 2,
            'level_id' => 2,
        ]);

        Enrollment::query()->create([
            'student_id' => 1,
            'session_id' => 3,
            'level_id' => 3,
        ]);

        Enrollment::query()->create([
            'student_id' => 1,
            'session_id' => 4,
            'level_id' => 4,
        ]);

        $this->call([
            ResultSeeder::class,
        ]);
    }
}
