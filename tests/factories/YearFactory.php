<?php

declare(strict_types=1);

namespace Tests\Factories;

use App\Models\Year;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Year> */
final class YearFactory extends Factory
{
    protected $model = Year::class;

    /** @return array<string, string> */
    public function definition(): array
    {
        return [
            'name' => fake()->unique()->year(),
        ];
    }
}
