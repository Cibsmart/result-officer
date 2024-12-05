<?php

declare(strict_types=1);

namespace Tests\Factories;

use App\Models\Semester;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Semester> */
final class SemesterFactory extends Factory
{
    protected $model = Semester::class;

    /** @return array<string, string> */
    public function definition(): array
    {
        return [
            'name' => fake()->unique()->randomElement(['FIRST', 'SECOND']),
            'slug' => fake()->slug(),
        ];
    }
}
