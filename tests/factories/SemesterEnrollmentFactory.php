<?php

namespace Tests\Factories;

use App\Models\SemesterEnrollment;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\SemesterEnrollment>
 */
class SemesterEnrollmentFactory extends Factory
{
    protected $model = SemesterEnrollment::class;

    public function definition(): array
    {
        return [
            'enrollment_id' => EnrollmentFactory::new(),
            'semester_id' => SemesterFactory::new(),
        ];
    }
}
