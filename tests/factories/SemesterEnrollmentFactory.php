<?php

declare(strict_types=1);

namespace Tests\Factories;

use App\Models\SemesterEnrollment;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\SemesterEnrollment> */
final class SemesterEnrollmentFactory extends Factory
{
    protected $model = SemesterEnrollment::class;

    /** @return array<string, string> */
    public function definition(): array
    {
        return [
            'semester_id' => SemesterFactory::new(),
            'session_enrollment_id' => SessionEnrollmentFactory::new(),
        ];
    }
}
