<?php

declare(strict_types=1);

namespace Tests\Factories;

use App\Models\LocalGovernment;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\LocalGovernment> */
final class LocalGovernmentFactory extends Factory
{
    protected $model = LocalGovernment::class;

    /** @return array<string, string|\Tests\Factories\CountryFactory> */
    public function definition(): array
    {
        return [
            'name' => fake()->unique()->country(),
            'state_id' => StateFactory::new(),
        ];
    }
}
