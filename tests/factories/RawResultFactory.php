<?php

declare(strict_types=1);

namespace Tests\Factories;

use App\Enums\Grade;
use App\Enums\RawDataStatus;
use App\Models\RawResult;
use App\Values\TotalScore;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\RawResult> */
final class RawResultFactory extends Factory
{
    protected $model = RawResult::class;

    /** @return array<string, string> */
    public function definition(): array
    {
        $inCourse = fake()->numberBetween(0, 30);
        $exam = fake()->numberBetween(0, 70);
        $total = TotalScore::new($inCourse + $exam);
        $grade = Grade::for($total);

        return [
            'exam' => $exam,
            'grade' => $grade->value,
            'import_event_id' => ImportEventFactory::new(),
            'in_course' => $inCourse,
            'lecturer_department' => fake()->country(),
            'lecturer_email' => fake()->email(),
            'lecturer_name' => fake()->name(),
            'lecturer_phone' => fake()->phoneNumber(),
            'online_id' => '1',
            'registration_id' => RegistrationFactory::new(),
            'registration_number' => 'EBSU/' . fake()->year() . '/' . fake()->unique()->randomNumber(5, true),
            'status' => RawDataStatus::PENDING,
            'total' => $total->value,
            'upload_date' => '20-08-2020',
        ];
    }
}
