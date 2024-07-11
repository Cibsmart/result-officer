<?php

declare(strict_types=1);

namespace Tests\Factories;

use App\Models\Faculty;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Faculty> */
final class FacultyFactory extends Factory
{
    protected $model = Faculty::class;

    /** @return array<string, string> */
    public function definition(): array
    {
        return [
            'code' => fake()->unique()->countryCode(),
            'name' => fake()->country(),
        ];
    }
}
