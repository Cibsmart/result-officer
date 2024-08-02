<?php

declare(strict_types=1);

namespace Tests\Factories;

use App\Enums\GenderEnum;
use App\Models\Student;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Student> */
final class StudentFactory extends Factory
{
    protected $model = Student::class;

    /** @return array<string, string> */
    public function definition(): array
    {
        $maximumDateOfBirth = now()->subYears(15)->format('Y-m-d');

        return [
            'date_of_birth' => fake()->date(max: $maximumDateOfBirth),
            'entry_level_id' => LevelFactory::new(),
            'entry_mode_id' => EntryModeFactory::new(),
            'entry_session_id' => SessionFactory::new(),
            'first_name' => fake()->firstName(),
            'gender' => fake()->randomElement(GenderEnum::cases()),
            'last_name' => fake()->lastName(),
            'other_names' => fake()->firstName(),
            'program_id' => ProgramFactory::new(),
            'registration_number' => 'EBSU/' . fake()->year() . '/' . fake()->unique()->randomNumber(5, true),
            'state_id' => StateFactory::new(),
        ];
    }
}
