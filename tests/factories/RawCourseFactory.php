<?php

declare(strict_types=1);

namespace Tests\Factories;

use App\Enums\RawDataStatus;
use App\Models\RawCourse;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\RawCourse> */
final class RawCourseFactory extends Factory
{
    protected $model = RawCourse::class;

    /** @return array<string, string> */
    public function definition(): array
    {
        return [
            'code' => mb_strtoupper(fake()->lexify('???')) . ' ' . fake()->randomNumber(3, true),
            'import_event_id' => ImportEventFactory::new(),
            'online_id' => '1',
            'status' => RawDataStatus::PENDING,
            'title' => fake()->country(),
        ];
    }
}
