<?php

declare(strict_types=1);

namespace Tests\Factories;

use App\Enums\CumulativeComputationStrategy;
use App\Models\Institution;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Institution> */
final class InstitutionFactory extends Factory
{
    protected $model = Institution::class;

    /** @return array<string, string> */
    public function definition(): array
    {
        return [
            'address' => fake()->unique()->city(),
            'code' => fake()->unique()->countryCode(),
            'name' => fake()->unique()->country(),
            'strategy' => fake()->randomElement(CumulativeComputationStrategy::cases()),
        ];
    }
}
