<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\CourseAlternative;
use Illuminate\Database\Seeder;

final class CourseAlternativeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        CourseAlternative::query()->create([
            'alternate_course_id' => 962,
            'original_course_id' => 963,
            'program_curriculum_course_id' => 58,
        ]);

        CourseAlternative::query()->create([
            'alternate_course_id' => 4870,
            'original_course_id' => 4869,
        ]);
    }
}
