<?php

declare(strict_types=1);

namespace Tests\Factories;

use App\Models\DBMail;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\DBMail> */
final class DBMailFactory extends Factory
{
    protected $model = DBMail::class;

    /** @return array<string, string> */
    public function definition(): array
    {
        return [
            'date' => fake()->date(),
            'title' => fake()->sentence(),
            'user_id' => UserFactory::new(),
        ];
    }
}
