<?php

declare(strict_types=1);

namespace Tests\Factories;

use App\Enums\ProgramDuration;
use App\Models\Program;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Program> */
final class ProgramFactory extends Factory
{
    protected $model = Program::class;

    /** @return array<string, string> */
    public function definition(): array
    {
        return [
            'code' => fake()->unique()->countryCode(),
            'department_id' => DepartmentFactory::new(),
            'duration' => ProgramDuration::FOUR->value,
            'name' => fake()->country(),
            'program_type_id' => ProgramTypeFactory::new(),
        ];
    }

    public function active(): self
    {
        // phpcs:ignore SlevomatCodingStandard.Functions.UnusedParameter
        return $this->state(fn (array $attributes) => [
            'is_active' => true,
        ]);
    }
}
