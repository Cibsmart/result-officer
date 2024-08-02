<?php

declare(strict_types=1);

namespace Tests\Factories;

use App\Models\EntryMode;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\EntryMode> */
final class EntryModeFactory extends Factory
{
    protected $model = EntryMode::class;

    /** @return array<string, string> */
    public function definition(): array
    {
        return [
            'code' => fake()->unique()->countryCode(),
            'name' => fake()->country(),
        ];
    }
}
