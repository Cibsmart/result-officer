<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\ProgramCourse;
use Illuminate\Database\Seeder;

final class ProgramCourseSeeder extends Seeder
{
    public function run(): void
    {
        ProgramCourse::query()->create([
            'course_id' => 1,
            'course_type_id' => 1,
            'credit_unit' => 3,
            'program_curriculum_id' => 1,
        ]);
    }
}
