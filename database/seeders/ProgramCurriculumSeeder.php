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
            'minimum_elective_units' => 2,
            'program_id' => 1,
            'semester_id' => 1,
            'slug' => 'CSC-BMAS-100-FIRST',
        ]);

        ProgramCurriculum::query()->create([
            'curriculum_id' => 1,
            'level_id' => 1,
            'minimum_elective_units' => 3,
            'program_id' => 1,
            'semester_id' => 2,
            'slug' => 'CSC-BMAS-100-SECOND',
        ]);
    }

}
