<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\ScoreType;
use Illuminate\Database\Seeder;

final class ScoreTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        ScoreType::query()->create([
            'maximum_score' => 30,
            'name' => 'course-work',
        ]);

        ScoreType::query()->create([
            'maximum_score' => 70,
            'name' => 'exam',
        ]);
    }
}
