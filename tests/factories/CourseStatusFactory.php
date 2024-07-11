<?php

declare(strict_types=1);

namespace Tests\Factories;

use App\Models\CourseStatus;
use Illuminate\Database\Eloquent\Factories\Factory;

final class CourseStatusFactory extends Factory
{
    protected $model = CourseStatus::class;

    /** @return array<string, string> */
    public function definition(): array
    {
        return [
            'code' => fake()->randomElement(['F', 'R']),
            'name' => fake()->word(),
        ];
    }
}
