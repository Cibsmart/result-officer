<?php

namespace Tests\Factories;

use App\Models\EntryMode;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\EntryMode>
 */
class EntryModeFactory extends Factory
{
    protected $model = EntryMode::class;

    /** @return array<string, string> */
    public function definition(): array
    {
        return [
            'code' => fake()->unique()->randomElement(['DE', 'UTME', 'TRANSFER']),
            'name' => fake()->country(),
        ];
    }
}
