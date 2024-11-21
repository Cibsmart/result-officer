<?php

declare(strict_types=1);

namespace Tests\Factories;

use App\Enums\VettingStatus;
use App\Enums\VettingType;
use App\Models\VettingStep;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\VettingStep> */
final class VettingStepFactory extends Factory
{
    protected $model = VettingStep::class;

    /** @return array<string, string> */
    public function definition(): array
    {
        return [
            'message' => '',
            'status' => VettingStatus::NEW,
            'type' => fake()->randomElement(VettingType::cases()),
            'vetting_event_id' => VettingEventFactory::new(),
        ];
    }
}
