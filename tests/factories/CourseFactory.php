<?php

declare(strict_types=1);

namespace Tests\Factories;

use App\Models\Course;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Course> */
final class CourseFactory extends Factory
{
    protected $model = Course::class;

    /** @return array<string, string> */
    public function definition(): array
    {
        return [
            'code' => fake()->unique()->countryCode(),
            'slug' => fake()->slug(),
            'title' => fake()->word(),
        ];
    }
}
