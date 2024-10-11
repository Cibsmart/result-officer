<?php

declare(strict_types=1);

namespace Tests\Factories;

use App\Enums\YearEnum;
use App\Models\Enrollment;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Student> */
final class EnrollmentFactory extends Factory
{
    protected $model = Enrollment::class;

    /** @return array<string, string> */
    public function definition(): array
    {
        return [
            'level_id' => LevelFactory::new(),
            'session_id' => SessionFactory::new(),
            'student_id' => StudentFactory::new(),
            'year' => YearEnum::FIRST,
        ];
    }
}
