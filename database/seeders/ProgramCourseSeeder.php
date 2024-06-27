<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\ProgramCourse;
use Illuminate\Database\Seeder;

final class ProgramCourseSeeder extends Seeder
{

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        ProgramCourse::query()->create([
            'curriculum_id' => 1,
            'level_id' => 1,
            'minimum_elective_units' => 2,
            'program_id' => 1,
            'semester_id' => 1,
        ]);

        ProgramCourse::query()->create([
            'curriculum_id' => 1,
            'level_id' => 1,
            'minimum_elective_units' => 3,
            'program_id' => 1,
            'semester_id' => 2,
        ]);
    }

}
