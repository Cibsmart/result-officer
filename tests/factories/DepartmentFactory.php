<?php

namespace Tests\Factories;

use App\Models\Department;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Department>
 */
class DepartmentFactory extends Factory
{
    protected $model = Department::class;

    /** @return array<string, mixed> */
    public function definition(): array
    {
        return [
            'faculty_id' => FacultyFactory::new(),
            'code' => fake()->unique()->countryCode,
            'name' => fake()->country,
        ];
    }
}
