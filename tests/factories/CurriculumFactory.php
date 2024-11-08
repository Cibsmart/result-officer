<?php

declare(strict_types=1);

namespace Tests\Factories;

use App\Models\Curriculum;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Curriculum> */
final class CurriculumFactory extends Factory
{
    protected $model = Curriculum::class;

    /** @return array<string, string> */
    public function definition(): array
    {
        return [
            'code' => fake()->unique()->countryCode(),
            'name' => fake()->countryISOAlpha3(),
        ];
    }
}
