<?php

declare(strict_types=1);

namespace Tests\Factories;

use App\Enums\CourseStatus;
use App\Enums\CreditUnit;
use App\Models\Registration;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Registration> */
final class RegistrationFactory extends Factory
{
    protected $model = Registration::class;

    /** @return array<string, string> */
    public function definition(): array
    {
        return [
            'course_id' => CourseFactory::new(),
            'course_status' => fake()->randomElement(CourseStatus::cases())->value,
            'credit_unit' => fake()->randomElement(CreditUnit::cases())->value,
            'semester_enrollment_id' => SemesterEnrollmentFactory::new(),
        ];
    }
}
