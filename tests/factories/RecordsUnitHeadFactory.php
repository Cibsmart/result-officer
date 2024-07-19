<?php

declare(strict_types=1);

namespace Tests\Factories;

use App\Models\RecordsUnitHead;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\RecordsUnitHead> */
final class RecordsUnitHeadFactory extends Factory
{
    protected $model = RecordsUnitHead::class;

    /** @return array<string, string|false> */
    public function definition(): array
    {
        return [
            'is_current' => false,
            'name' => fake()->name(),
        ];
    }

    /** @return \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\RecordsUnitHead> */
    public function active(): Factory
    {
        return $this->state(state: fn (array $attributes) => ['is_current' => true]);
    }
}
