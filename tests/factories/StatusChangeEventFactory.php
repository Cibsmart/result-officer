<?php

declare(strict_types=1);

namespace Tests\Factories;

use App\Enums\StudentStatus;
use App\Models\StatusChangeEvent;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\StatusChangeEvent> */
final class StatusChangeEventFactory extends Factory
{
    protected $model = StatusChangeEvent::class;

    /** @return array<string, string|\Tests\Factories\StudentFactory> */
    public function definition(): array
    {
        return [
            'date' => now(),
            'status' => StudentStatus::NEW,
            'student_id' => StudentFactory::new(),
        ];
    }

    public function cleared(): self
    {
        // phpcs:ignore SlevomatCodingStandard.Functions.UnusedParameter
        return $this->state(fn (array $attributes) => [
            'status' => StudentStatus::CLEARED,
        ]);
    }
}
