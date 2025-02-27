<?php

declare(strict_types=1);

namespace Tests\Factories;

use App\Models\Session;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/** @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Session> */
final class SessionFactory extends Factory
{
    protected $model = Session::class;

    /** @return array<string, string> */
    public function definition(): array
    {
        $years = collect(range(2_000, now()->year))
            ->reject(fn ($year) => $year === 2_018)
            ->map(fn ($year) => $year . '/' . ($year + 1));

        return [
            'name' => fake()->unique()->randomElement($years),
            'slug' => $this->slug(...),
        ];
    }

    /** @param array<string, string> $values */
    private function slug(array $values): string
    {
        return Str::of($values['name'])->replace('/', '-')->slug()->value();
    }
}
