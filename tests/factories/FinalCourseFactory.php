<?php

declare(strict_types=1);

namespace Tests\Factories;

use App\Models\FinalCourse;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\FinalCourse> */
final class FinalCourseFactory extends Factory
{
    protected $model = FinalCourse::class;

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
