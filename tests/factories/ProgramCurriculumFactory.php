<?php

declare(strict_types=1);

namespace Tests\Factories;

use App\Enums\EntryMode;
use App\Models\ProgramCurriculum;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ProgramCurriculum> */
final class ProgramCurriculumFactory extends Factory
{
    protected $model = ProgramCurriculum::class;

    /** @return array<string, string> */
    public function definition(): array
    {
        return [
            'curriculum_id' => CurriculumFactory::new(),
            'entry_mode' => EntryMode::UTME,
            'entry_session_id' => SessionFactory::new(),
            'program_id' => ProgramFactory::new(),
        ];
    }
}
