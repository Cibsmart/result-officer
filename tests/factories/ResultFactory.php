<?php

declare(strict_types=1);

namespace Tests\Factories;

use App\Enums\Grade;
use App\Models\Registration;
use App\Models\Result;
use App\Values\TotalScore;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Result> */
final class ResultFactory extends Factory
{
    protected $model = Result::class;

    /** @return array<string, string> */
    public function definition(): array
    {
        $inCourse = fake()->numberBetween(0, 30);
        $exam = fake()->numberBetween(0, 70);
        $scores = ['in-course' => $inCourse, 'exam' => $exam];
        $score = TotalScore::new($inCourse + $exam);
        $grade = Grade::for($score, fake()->randomElement([true, false]));
        $data = "$score->value, $grade->name $grade->value";

        return [
            'registration_id' => RegistrationFactory::new(),
            'data' => $data,
            'grade' => $grade->name,
            'grade_point' => fn (array $attributes,
            ) => $grade->point() * Registration::find($attributes['registration_id'])->credit_unit,
            'scores' => json_encode($scores),
            'total_score' => $score->value,
        ];
    }
}
