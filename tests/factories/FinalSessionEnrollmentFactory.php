<?php

declare(strict_types=1);

namespace Tests\Factories;

use App\Enums\Year;
use App\Models\FinalSessionEnrollment;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\FinalSessionEnrollment> */
final class FinalSessionEnrollmentFactory extends Factory
{
    protected $model = FinalSessionEnrollment::class;

    /** @return array<string, string> */
    public function definition(): array
    {
        return [
            'final_student_id' => FinalStudentFactory::new(),
            'level_id' => LevelFactory::new(),
            'session_id' => SessionFactory::new(),
            'student_id' => StudentFactory::new(),
            'year' => Year::FIRST,
        ];
    }
}
