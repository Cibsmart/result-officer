<?php

declare(strict_types=1);

namespace Tests\Factories;

use App\Enums\GenderEnum;
use App\Models\Student;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Student> */
class StudentFactory extends Factory
{
    protected $model = Student::class;

    /**
     * @return array<string, string>
     */
    public function definition(): array
    {
        $maximumDateOfBirth = now()->subYears(15)->format('Y-m-d');

        return [
            'matriculation_number' => 'EBSU/2009/51486',
            'last_name' => fake()->lastName(),
            'first_name' => fake()->firstName(),
            'other_names' => fake()->firstName(),
            'gender' => fake()->randomElement(GenderEnum::cases()),
            'date_of_birth' => fake()->date(max: $maximumDateOfBirth),
            'country_id' => CountryFactory::new(),
            'program_id' => ProgramFactory::new(),
            'entry_session_id' => SessionFactory::new(),
            'entry_level_id' => LevelFactory::new(),
            'entry_mode_id' => EntryModeFactory::new(),
        ];
    }
}
