<?php

declare(strict_types=1);

namespace Tests\Factories;

use App\Models\ExamOfficer;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ExamOfficer> */
final class ExamOfficerFactory extends Factory
{
    protected $model = ExamOfficer::class;

    /** @return array<string, string> */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
        ];
    }
}
