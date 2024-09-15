<?php

declare(strict_types=1);

namespace Tests\Factories;

use App\Enums\ImportEventStatus;
use App\Enums\ImportEventType;
use App\Models\ImportEvent;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ImportEvent> */
final class ImportEventFactory extends Factory
{
    protected $model = ImportEvent::class;

    /** @return array<string, string> */
    public function definition(): array
    {
        return [
            'count' => 0,
            'data' => ['courses' => 'all'],
            'status' => ImportEventStatus::STARTED,
            'type' => fake()->randomElement(ImportEventType::cases()),
            'user_id' => UserFactory::new(),
        ];
    }
}
