<?php

declare(strict_types=1);

namespace Tests\Factories;

use App\Models\FinalSemesterEnrollment;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\FinalSemesterEnrollment> */
final class FinalSemesterEnrollmentFactory extends Factory
{
    protected $model = FinalSemesterEnrollment::class;

    /** @return array<string, string> */
    public function definition(): array
    {
        return [
            'final_session_enrollment_id' => FinalSessionEnrollmentFactory::new(),
            'semester_id' => SemesterFactory::new(),
        ];
    }
}
