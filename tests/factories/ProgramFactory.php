<?php

namespace Tests\Factories;

use App\Models\Program;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Program>
 */
class ProgramFactory extends Factory
{
    protected $model = Program::class;

    /**
     * Define the model's default state.
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'department_id' => DepartmentFactory::new(),
            'code' => fake()->unique()->countryCode(),
            'name' => fake()->country(),
        ];
    }
}
