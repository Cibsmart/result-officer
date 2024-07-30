<?php

declare(strict_types=1);

namespace Tests\Factories;

use App\Models\Country;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Country> */
final class CountryFactory extends Factory
{
    protected $model = Country::class;

    /** @return array<string, string> */
    public function definition(): array
    {
        return [
            'code' => fake()->unique()->countryCode(),
            'demonym' => fake()->country(),
            'name' => fake()->unique()->country(),
        ];
    }
}
