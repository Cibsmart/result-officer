<?php

declare(strict_types=1);

namespace Tests\Factories;

use App\Models\ProgramCurriculumElectiveGroup;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ProgramCurriculumElectiveGroup> */
final class ProgramCurriculumElectiveGroupFactory extends Factory
{
    protected $model = ProgramCurriculumElectiveGroup::class;

    /**
     * {@inheritDoc}
     */
    public function definition(): array
    {
        return [
            'name' => fake()->countryCode(),
            'program_curriculum_semester_id' => ProgramCurriculumSemesterFactory::new(),
        ];
    }
}
