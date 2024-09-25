<?php

declare(strict_types=1);

namespace Tests\Factories;

use App\Enums\ImportEventMethod;
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
            'data' => ['courses' => 'all'],
            'downloaded' => 0,
            'duplicate' => 0,
            'failed' => 0,
            'method' => ImportEventMethod::ALL,
            'processed' => 0,
            'saved' => 0,
            'status' => ImportEventStatus::NEW,
            'type' => fake()->randomElement(ImportEventType::cases()),
            'user_id' => UserFactory::new(),
        ];
    }
}
