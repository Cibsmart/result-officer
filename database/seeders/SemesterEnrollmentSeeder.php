<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\SemesterEnrollment;
use Illuminate\Database\Seeder;

final class SemesterEnrollmentSeeder extends Seeder
{
    private array $semesters = [
        ['enrollment_id' => 1, 'semester_id' => 1],
        ['enrollment_id' => 2, 'semester_id' => 1],
        ['enrollment_id' => 2, 'semester_id' => 2],
        ['enrollment_id' => 3, 'semester_id' => 1],
        ['enrollment_id' => 3, 'semester_id' => 2],
        ['enrollment_id' => 4, 'semester_id' => 1],
        ['enrollment_id' => 4, 'semester_id' => 2],
        ['enrollment_id' => 5, 'semester_id' => 1],
        ['enrollment_id' => 5, 'semester_id' => 2],
    ];

    public function run(): void
    {
        foreach ($this->semesters as $semester) {
            SemesterEnrollment::query()->create([
                'enrollment_id' => $semester['enrollment_id'],
                'semester_id' => $semester['semester_id'],
            ]);
        }
    }
}
