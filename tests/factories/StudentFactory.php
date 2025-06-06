<?php

declare(strict_types=1);

namespace Tests\Factories;

use App\Enums\EntryMode;
use App\Enums\Gender;
use App\Enums\StudentStatus;
use App\Models\Student;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/** @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Student> */
final class StudentFactory extends Factory
{
    protected $model = Student::class;

    /** @return array<string, string> */
    public function definition(): array
    {
        $maximumDateOfBirth = now()->subYears(15)->format('Y-m-d');

        $gender = fake()->randomElement(Gender::cases());

        $randomNumber = fake()->unique()->randomNumber(5, true);

        $registrationNumber = 'EBSU/' . fake()->year() . '/' . $randomNumber;

        return [
            'date_of_birth' => fake()->date(max: $maximumDateOfBirth),
            'entry_level_id' => LevelFactory::new(),
            'entry_mode' => EntryMode::UTME,
            'entry_session_id' => SessionFactory::new(),
            'first_name' => fake()->firstName(),
            'gender' => $gender->value,
            'last_name' => fake()->lastName(),
            'local_government_id' => LocalGovernmentFactory::new(),
            'number' => $randomNumber,
            'other_names' => fake()->firstName(),
            'program_id' => ProgramFactory::new(),
            'registration_number' => $registrationNumber,
            'slug' => Str::of($registrationNumber)->replace('/', '-')->slug()->value(),
            'status' => StudentStatus::NEW,
        ];
    }

    public function cleared(): self
    {
        // phpcs:ignore SlevomatCodingStandard.Functions.UnusedParameter
        return $this->state(fn (array $attributes) => [
            'status' => StudentStatus::CLEARED,
        ]);
    }

    public function graduated(): self
    {
        // phpcs:ignore SlevomatCodingStandard.Functions.UnusedParameter
        return $this->state(fn (array $attributes) => [
            'status' => StudentStatus::GRADUATED,
        ]);
    }
}
