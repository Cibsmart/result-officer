<?php

declare(strict_types=1);

namespace Tests\Factories;

use App\Models\Department;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Department> */
final class DepartmentFactory extends Factory
{
    protected $model = Department::class;

    /** @return array<string, string> */
    public function definition(): array
    {
        return [
            'code' => fake()->unique()->countryCode(),
            'faculty_id' => FacultyFactory::new(),
            'name' => fake()->country(),
        ];
    }
}
