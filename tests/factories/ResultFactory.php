<?php

namespace Tests\Factories;

use App\Models\Result;
use App\Services\Grader;
use App\Values\TotalScore;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Result>
 */
class ResultFactory extends Factory
{
    protected $model = Result::class;

    public function definition(): array
    {
        $inCourse = fake()->numberBetween(0, 30);
        $exam = fake()->numberBetween(0, 70);
        $scores = ['in-course' => $inCourse, 'exam' => $exam];
        $score = TotalScore::new($inCourse + $exam);
        $grade = Grader::new($score, fake()->randomElement([true, false]))->grade();
        $data = "$score->value, $grade->name $grade->value";

        return [
            'course_registration_id' => CourseRegistrationFactory::new(),
            'scores' => json_encode($scores),
            'total_score' => $score->value,
            'grade' => $grade->name,
            'grade_point' => $grade->value,
            'data' => $data,
        ];
    }
}
