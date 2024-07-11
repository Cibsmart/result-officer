<?php

namespace Tests\Factories;

use App\Enums\CreditUnitEnum;
use App\Models\CourseRegistration;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\CourseRegistration>
 */
class CourseRegistrationFactory extends Factory
{
    protected $model = CourseRegistration::class;

    public function definition(): array
    {
        return [
            'credit_unit' => (fake()->randomElement(CreditUnitEnum::cases()))->value,
            'semester_enrollment_id' => SemesterEnrollmentFactory::new(),
            'course_id' => CourseFactory::new(),
            'course_status_id' => CourseStatusFactory::new(),
        ];
    }
}
