<?php

namespace Tests\Factories;

use App\Models\CourseStatus;
use Illuminate\Database\Eloquent\Factories\Factory;

class CourseStatusFactory extends Factory
{
    protected $model = CourseStatus::class;

    public function definition(): array
    {
        return [
            'code' => fake()->randomElement(['F', 'R']),
            'name' => fake()->word(),
        ];
    }
}
