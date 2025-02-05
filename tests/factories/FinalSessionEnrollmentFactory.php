<?php

declare(strict_types=1);

namespace Tests\Factories;

use App\Enums\Year;
use App\Models\FinalSessionEnrollment;
use App\Models\SessionEnrollment;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\FinalSessionEnrollment> */
final class FinalSessionEnrollmentFactory extends Factory
{
    protected $model = FinalSessionEnrollment::class;

    /** @return array<string, string> */
    public function definition(): array
    {
        return [
            'level_id' => LevelFactory::new(),
            'session_id' => SessionFactory::new(),
            'student_id' => StudentFactory::new(),
            'final_student_id' => FinalStudentFactory::new(),
            'year' => Year::FIRST,
        ];
    }
}
