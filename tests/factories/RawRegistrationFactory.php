<?php

declare(strict_types=1);

namespace Tests\Factories;

use App\Enums\CreditUnit;
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
        $level = LevelFactory::new()->createOne();
        $session = SessionFactory::new()->createOne();
        $student = StudentFactory::new()->createOne([
            'entry_level_id' => $level->id, 'entry_session_id' => $session->id,
        ]);
        $semester = SemesterFactory::new()->createOne();

        return [
            'course_id' => CourseFactory::new(),
            'course_title' => fake()->name(),
            'credit_unit' => fake()->randomElement(CreditUnit::cases())->value,
            'import_event_id' => ImportEventFactory::new(),
            'level' => $level->name,
            'online_id' => '1',
            'registration_date' => '20-12-2009',
            'registration_number' => $student->registration_number,
            'semester' => $semester->name,
            'session' => $session->name,
            'status' => RawDataStatus::PENDING,
        ];
    }
}
