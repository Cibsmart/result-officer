<?php

namespace Database\Seeders;

use App\Models\ScoreType;
use Illuminate\Database\Seeder;

class ScoreTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        ScoreType::query()->create([
            'name' => 'course-work',
            'maximum_score' => 30,
        ]);

        ScoreType::query()->create([
            'name' => 'exam',
            'maximum_score' => 70,
        ]);
    }
}
