<?php

declare(strict_types=1);

namespace Tests\Factories;

use App\Models\ProgramCurriculumElectiveCourse;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ProgramCurriculumElectiveCourse> */
final class ProgramCurriculumElectiveCourseFactory extends Factory
{
    protected $model = ProgramCurriculumElectiveCourse::class;

    /**
     * {@inheritDoc}
     */
    public function definition(): array
    {
        return [
            'program_curriculum_course_id' => ProgramCurriculumCourseFactory::new(),
            'program_curriculum_elective_group_id' => ProgramCurriculumElectiveGroupFactory::new(),
        ];
    }
}
