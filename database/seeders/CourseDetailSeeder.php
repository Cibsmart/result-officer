<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\CourseDetail;
use Illuminate\Database\Seeder;

final class CourseDetailSeeder extends Seeder
{

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        CourseDetail::query()->create([
            'course_id' => 1,
            'course_type_id' => 1,
            'credit_unit_id' => 3,
            'program_course_id' => 1,
        ]);
    }

}
