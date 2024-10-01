<?php

declare(strict_types=1);

namespace Tests\Factories;

use App\Enums\RawDataStatus;
use App\Models\RawDepartment;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\RawDepartment> */
final class RawDepartmentFactory extends Factory
{
    protected $model = RawDepartment::class;

    /** @return array<string, string> */
    public function definition(): array
    {
        return [
            'code' => fake()->countryCode(),
            'faculty' => fake()->country(),
            'import_event_id' => ImportEventFactory::new(),
            'name' => fake()->country(),
            'online_id' => '1',
            'options' => [],
            'status' => RawDataStatus::PENDING,
        ];
    }
}
