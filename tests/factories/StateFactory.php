<?php

declare(strict_types=1);

namespace Tests\Factories;

use App\Models\State;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\State> */
final class StateFactory extends Factory
{
    protected $model = State::class;

    /** @return array<string, string|\Tests\Factories\CountryFactory> */
    public function definition(): array
    {
        return [
            'country_id' => CountryFactory::new(),
            'name' => fake()->unique()->country(),
        ];
    }
}
