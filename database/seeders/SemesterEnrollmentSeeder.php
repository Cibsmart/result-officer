<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\SemesterEnrollment;
use Illuminate\Database\Seeder;

final class SemesterEnrollmentSeeder extends Seeder
{
    private array $semesters = [
        ['session_enrollment_id' => 1, 'semester_id' => 1],
        ['session_enrollment_id' => 2, 'semester_id' => 1],
        ['session_enrollment_id' => 2, 'semester_id' => 2],
        ['session_enrollment_id' => 3, 'semester_id' => 1],
        ['session_enrollment_id' => 3, 'semester_id' => 2],
        ['session_enrollment_id' => 4, 'semester_id' => 1],
        ['session_enrollment_id' => 4, 'semester_id' => 2],
        ['session_enrollment_id' => 5, 'semester_id' => 1],
        ['session_enrollment_id' => 5, 'semester_id' => 2],
        ['session_enrollment_id' => 6, 'semester_id' => 1],
        ['session_enrollment_id' => 6, 'semester_id' => 2],
        ['session_enrollment_id' => 7, 'semester_id' => 1],
        ['session_enrollment_id' => 7, 'semester_id' => 2],
        ['session_enrollment_id' => 8, 'semester_id' => 1],
        ['session_enrollment_id' => 8, 'semester_id' => 2],
        ['session_enrollment_id' => 9, 'semester_id' => 1],
        ['session_enrollment_id' => 9, 'semester_id' => 2],
    ];

    public function run(): void
    {
        foreach ($this->semesters as $semester) {
            SemesterEnrollment::query()->create([
                'session_enrollment_id' => $semester['session_enrollment_id'],
                'semester_id' => $semester['semester_id'],
            ]);
        }
    }
}
