<?php

declare(strict_types=1);

namespace Tests\Factories;

use App\Enums\CreditUnitEnum;
use App\Models\CourseRegistration;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\CourseRegistration> */
final class CourseRegistrationFactory extends Factory
{
    protected $model = CourseRegistration::class;

    /** @return array<string, string> */
    public function definition(): array
    {
        return [
            'course_id' => CourseFactory::new(),
            'course_status_id' => CourseStatusFactory::new(),
            'credit_unit' => (fake()->randomElement(CreditUnitEnum::cases()))->value,
            'semester_enrollment_id' => SemesterEnrollmentFactory::new(),
        ];
    }
}
