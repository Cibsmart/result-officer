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
        $scores = ['in_course' => $inCourse, 'exam' => $exam];
        $score = TotalScore::new($inCourse + $exam);
        $grade = Grade::for($score, fake()->randomElement([true, false]));

        return [
            'grade' => $grade->name,
            'grade_point' => $this->gradePoint(...),
            'registration_id' => RegistrationFactory::new(),
            'scores' => $scores,
            'total_score' => $score->value,
        ];
    }

    public function configure(): self
    {
        return $this->afterCreating(function (Result $result): void {
            $data = $result->getData();
            $result->resultDetail()->create(['value' => $data, 'validate' => false]);
        });
    }

    /** @param array<string, string> $values */
    private function gradePoint(array $values): int
    {
        $grade = Grade::from($values['grade']);
        $registration = Registration::find($values['registration_id']);

        return $grade->point() * $registration->credit_unit->value;
    }
}
