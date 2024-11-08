<?php

declare(strict_types=1);

namespace Tests\Factories;

use App\Enums\VettingEventStatus;
use App\Models\VettingEvent;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\VettingEvent> */
final class VettingEventFactory extends Factory
{
    protected $model = VettingEvent::class;

    /** @return array<string, string> */
    public function definition(): array
    {
        return [
            'program_curriculum_id' => ProgramCurriculumFactory::new(),
            'status' => fake()->randomElement(VettingEventStatus::cases()),
            'student_id' => StudentFactory::new(),
        ];
    }
}
