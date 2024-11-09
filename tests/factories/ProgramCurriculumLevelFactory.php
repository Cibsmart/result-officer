<?php

declare(strict_types=1);

namespace Tests\Factories;

use App\Models\ProgramCurriculumLevel;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ProgramCurriculumLevel> */
final class ProgramCurriculumLevelFactory extends Factory
{
    protected $model = ProgramCurriculumLevel::class;

    /**
     * {@inheritDoc}
     */
    public function definition(): array
    {
        return [
            'level_id' => LevelFactory::new(),
            'program_curriculum_id' => ProgramCurriculumFactory::new(),
        ];
    }
}
