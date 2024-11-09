<?php

declare(strict_types=1);

namespace Tests\Factories;

use App\Enums\CourseType;
use App\Enums\CreditUnit;
use App\Models\ProgramCurriculumCourse;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ProgramCurriculumCourse> */
final class ProgramCurriculumCourseFactory extends Factory
{
    protected $model = ProgramCurriculumCourse::class;

    /**
     * {@inheritDoc}
     */
    public function definition(): array
    {
        return [
            'course_id' => CourseFactory::new(),
            'course_type' => fake()->randomElement(CourseType::cases()),
            'credit_unit' => fake()->randomElement(CreditUnit::cases()),
            'program_curriculum_semester_id' => ProgramCurriculumSemesterFactory::new(),

        ];
    }
}
