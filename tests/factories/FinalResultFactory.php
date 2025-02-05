<?php

declare(strict_types=1);

namespace Tests\Factories;

use App\Enums\CourseStatus;
use App\Enums\CreditUnit;
use App\Enums\Grade;
use App\Enums\RecordSource;
use App\Models\FinalResult;
use App\Models\FinalSemesterEnrollment;
use App\Models\Registration;
use App\Models\Result;
use App\Values\TotalScore;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\FinalResult> */
final class FinalResultFactory extends Factory
{
    protected $model = FinalResult::class;

    /** @return array<string, string> */
    public function definition(): array
    {
        $inCourse = fake()->numberBetween(0, 30);
        $exam = fake()->numberBetween(0, 70);

        return [
            'final_course_id' => FinalCourseFactory::new(),
            'course_status' => CourseStatus::FRESH->value,
            'credit_unit' => CreditUnit::THREE->value,
            'final_semester_enrollment_id' => FinalSemesterEnrollmentFactory::new(),
            'grade' => Grade::for(score: TotalScore::new($inCourse + $exam))->name,
            'grade_point' => $this->gradePoint(...),
            'scores' => json_encode(['in_course' => $inCourse, 'exam' => $exam]),
            'total_score' => TotalScore::new($inCourse + $exam)->value,
            'source' => RecordSource::SYSTEM->value,
        ];
    }

    /** @param array<string, int|string> $values */
    private function gradePoint(array $values): int
    {
        $grade = Grade::from($values['grade']);
        $creditUnit = CreditUnit::from($values['credit_unit']);

        return $grade->point() * $creditUnit->value;
    }
}
