<?php

declare(strict_types=1);

namespace Tests\Factories;

use App\Enums\LevelEnum;
use App\Models\Level;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Level> */
final class LevelFactory extends Factory
{
    protected $model = Level::class;

    /** @return array<string, string> */
    public function definition(): array
    {
        $level = fake()->unique()->randomElement(LevelEnum::cases());

        return [
            'name' => $level->value,
            'slug' => fake()->slug(),
        ];
    }
}
