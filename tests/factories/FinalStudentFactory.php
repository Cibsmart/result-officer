<?php

declare(strict_types=1);

namespace Tests\Factories;

use App\Models\FinalStudent;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\FinalStudent> */
final class FinalStudentFactory extends Factory
{
    protected $model = FinalStudent::class;

    /** @return array<string, string> */
    public function definition(): array
    {
        return [
            'exam_officer_id' => ExamOfficerFactory::new(),
            'month' => fake()->monthName(),
            'student_id' => StudentFactory::new(),
            'user_id' => UserFactory::new(),
            'year' => fake()->year(),
        ];
    }
}
