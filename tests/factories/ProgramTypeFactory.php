<?php

declare(strict_types=1);

namespace Tests\Factories;

use App\Models\ProgramType;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ProgramType> */
final class ProgramTypeFactory extends Factory
{
    protected $model = ProgramType::class;

    /** @return array<string, string> */
    public function definition(): array
    {
        return [
            'code' => fake()->unique()->countryCode(),
            'name' => fake()->country(),
        ];
    }
}
