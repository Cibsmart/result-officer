<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\ProgramCurriculum;
use Illuminate\Database\Seeder;

final class ProgramCurriculumSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        ProgramCurriculum::query()->create([
            'curriculum_id' => 1,
            'level_id' => 1,
            'minimum_elective_units' => 4,
            'program_id' => 1,
            'semester_id' => 1,
            'session_id' => 1,
        ]);

        ProgramCurriculum::query()->create([
            'curriculum_id' => 1,
            'level_id' => 1,
            'minimum_elective_units' => 3,
            'program_id' => 1,
            'semester_id' => 2,
            'session_id' => 1,
        ]);

        $this->call([ProgramCurriculumCourseSeeder::class]);
    }
}
