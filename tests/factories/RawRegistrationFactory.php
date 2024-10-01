<?php

declare(strict_types=1);

namespace Tests\Factories;

use App\Enums\RawDataStatus;
use App\Models\RawRegistration;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\RawRegistration> */
final class RawRegistrationFactory extends Factory
{
    protected $model = RawRegistration::class;

    /** @return array<string, string> */
    public function definition(): array
    {
        $level = LevelFactory::new()->createOne(['name' => fake()->randomElement(['100', '200'])]);
        $session = SessionFactory::new()->createOne();

        return [
            'course_id' => CourseFactory::new(),
            'course_title' => fake()->name(),
            'credit_unit' => fake()->randomDigitNotZero(),
            'import_event_id' => ImportEventFactory::new(),
            'level' => $level->name,
            'online_id' => '1',
            'registration_date' => '20-12-2009',
            'registration_number' => 'EBSU/' . fake()->year() . '/' . fake()->unique()->randomNumber(5, true),
            'semester' => fake()->randomElement(['FIRST', 'SECOND']),
            'session' => $session->name,
            'status' => RawDataStatus::PENDING,
        ];
    }
}
