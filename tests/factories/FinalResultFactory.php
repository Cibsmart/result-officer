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
        $scores = ['in_course' => $inCourse, 'exam' => $exam];
        $score = TotalScore::new($inCourse + $exam);
        $grade = Grade::for(score: $score);
        $creditUnit = CreditUnit::THREE->value;
        $gradePoint = $grade->point() * $creditUnit;


        return [
            'final_course_id' => FinalCourseFactory::new(),
            'course_status' => CourseStatus::FRESH->value,
            'credit_unit' => $creditUnit,
            'final_semester_enrollment_id' => FinalSemesterEnrollmentFactory::new(),
            'grade' => $grade->name,
            'grade_point' => $gradePoint,
            'scores' => json_encode($scores),
            'total_score' => $score->value,
            'source' => RecordSource::SYSTEM->value,
        ];
    }
}
