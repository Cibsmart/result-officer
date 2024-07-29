<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Enrollment;
use Illuminate\Database\Seeder;

final class EnrollmentSeeder extends Seeder
{
    private array $enrollments = [
        1 => [
            1 => ['level_id' => 1, 'session_id' => 1],
        ],
        2 => [
            1 => ['level_id' => 1, 'session_id' => 1],
            2 => ['level_id' => 2, 'session_id' => 2],
            3 => ['level_id' => 3, 'session_id' => 3],
            4 => ['level_id' => 4, 'session_id' => 4],
        ],
        3 => [
            1 => ['level_id' => 1, 'session_id' => 1],
            2 => ['level_id' => 2, 'session_id' => 2],
            3 => ['level_id' => 3, 'session_id' => 3],
            4 => ['level_id' => 4, 'session_id' => 4],
        ],
    ];

    public function run(): void
    {
        $this->call([
            YearSeeder::class,
        ]);

        foreach ($this->enrollments as $student_id => $enrollments) {
            foreach ($enrollments as $year_id => $enrollment) {
                Enrollment::query()->create([
                    'level_id' => $enrollment['level_id'],
                    'session_id' => $enrollment['session_id'],
                    'student_id' => $student_id,
                    'year_id' => $year_id,
                ]);
            }
        }

        $this->call([
            SemesterEnrollmentSeeder::class,
            ResultSeeder::class,
        ]);
    }
}
