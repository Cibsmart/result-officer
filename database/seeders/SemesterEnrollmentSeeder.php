<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\SemesterEnrollment;
use Illuminate\Database\Seeder;

final class SemesterEnrollmentSeeder extends Seeder
{
    public function run(): void
    {
        SemesterEnrollment::query()->create([
            'enrollment_id' => 1,
            'semester_id' => 1,
        ]);

        SemesterEnrollment::query()->create([
            'enrollment_id' => 2,
            'semester_id' => 1,
        ]);

        SemesterEnrollment::query()->create([
            'enrollment_id' => 2,
            'semester_id' => 2,
        ]);
    }
}
