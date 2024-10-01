<?php

declare(strict_types=1);

namespace Tests\Factories;

use App\Enums\Gender;
use App\Enums\RawDataStatus;
use App\Models\RawStudent;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\RawStudent> */
final class RawStudentFactory extends Factory
{
    protected $model = RawStudent::class;

    /** @return array<string, string> */
    public function definition(): array
    {
        $gender = fake()->randomElement(Gender::cases());

        return [
            'date_of_birth' => '27-12-1996',
            'department_id' => '1',
            'email' => 'test@gmail.com',
            'entry_level' => '100',
            'entry_mode' => 'UTME',
            'entry_session' => '2009-2010',
            'first_name' => fake()->firstName(),
            'gender' => $gender->value,
            'import_event_id' => ImportEventFactory::new(),
            'jamb_registration_number' => '12345678GH',
            'last_name' => fake()->lastName(),
            'local_government' => 'ABAKALIKI',
            'online_id' => '1',
            'option' => '',
            'other_names' => fake()->name(),
            'phone_number' => '703-324-3322',
            'registration_number' => 'EBSU/' . fake()->year() . '/' . fake()->unique()->randomNumber(5, true),
            'state' => 'EBONYI',
            'status' => RawDataStatus::PENDING,
        ];
    }
}
