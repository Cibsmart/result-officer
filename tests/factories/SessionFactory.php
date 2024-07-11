<?php

namespace Tests\Factories;

use App\Models\Session;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Session>
 */
class SessionFactory extends Factory
{
    protected $model = Session::class;

    /** @return array<string, string> */
    public function definition(): array
    {
        $year = (int) fake()->unique()->year();
        $next = $year + 1;

        $session = "$year/$next";

        return [
            'name' => $session,
        ];
    }
}
