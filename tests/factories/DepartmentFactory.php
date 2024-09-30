<?php

declare(strict_types=1);

namespace Tests\Factories;

use App\Models\Department;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Department> */
final class DepartmentFactory extends Factory
{
    protected $model = Department::class;

    /** @return array<string, string|false|\Tests\Factories\FacultyFactory> */
    public function definition(): array
    {
        return [
            'code' => fake()->unique()->countryCode(),
            'faculty_id' => FacultyFactory::new(),
            'is_active' => false,
            'name' => fake()->country(),
        ];
    }

    public function configure(): self
    {
        return $this->afterCreating(function (Department $department): void {
            ProgramFactory::new()->createOne(['department_id' => $department->id, 'name' => $department->name]);
        });
    }

    public function active(): self
    {
        // phpcs:ignore SlevomatCodingStandard.Functions.UnusedParameter
        return $this->state(fn (array $attributes) => [
            'is_active' => true,
        ]);
    }
}
