<?php

declare(strict_types=1);

namespace Tests\Factories;

use App\Enums\CreditUnit;
use App\Models\ProgramCurriculumSemester;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ProgramCurriculumSemester> */
final class ProgramCurriculumSemesterFactory extends Factory
{
    protected $model = ProgramCurriculumSemester::class;

    /**
     * {@inheritDoc}
     */
    public function definition(): array
    {
        return [
            'program_curriculum_level_id' => ProgramCurriculumLevelFactory::new(),
            'semester_id' => SemesterFactory::new(),
            'minimum_credit_units' => CreditUnit::FIFTEEN->value,
            'maximum_credit_units' => CreditUnit::TWENTYFOUR->value,
        ];
    }
}
