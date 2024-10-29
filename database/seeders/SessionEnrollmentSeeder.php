<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\SessionEnrollment;
use Illuminate\Database\Seeder;

final class SessionEnrollmentSeeder extends Seeder
{
    private array $sessionEnrollments = [
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
        foreach ($this->sessionEnrollments as $student_id => $sessionEnrollments) {
            foreach ($sessionEnrollments as $year_id => $sessionEnrollment) {
                SessionEnrollment::query()->create([
                    'level_id' => $sessionEnrollment['level_id'],
                    'session_id' => $sessionEnrollment['session_id'],
                    'student_id' => $student_id,
                    'year' => $year_id,
                ]);
            }
        }

        $this->call([
            SemesterEnrollmentSeeder::class,
            ResultSeeder::class,
        ]);
    }
}
