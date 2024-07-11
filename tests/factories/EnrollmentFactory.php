<?php

namespace Tests\Factories;

use App\Models\Enrollment;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Student> */
class EnrollmentFactory extends Factory
{
    protected $model = Enrollment::class;

    /**
     * @return array<string, string>
     */
    public function definition(): array
    {
        return [
            'student_id' => StudentFactory::new(),
            'session_id' => SessionFactory::new(),
            'level_id' => LevelFactory::new(),
            'year_id' => YearFactory::new(),
        ];
    }
}
