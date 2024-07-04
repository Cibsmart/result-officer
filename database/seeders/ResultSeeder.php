<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Result;
use Illuminate\Database\Seeder;

final class ResultSeeder extends Seeder
{
    public function run(): void
    {
        //BIO 101
        Result::query()->create([
            'course_id' => 1,
            'course_status_id' => 1,
            'credit_unit_id' => 3,
            'data' => '1 1 1 3 50 C 9',
            'enrollment_id' => 1,
            'grade' => 'C',
            'grade_point' => 9,
            'scores' => json_encode(['course-work' => 15, 'exam' => 35]),
            'semester_id' => 1,
            'total_score' => 50,
        ]);

        //BIO 107
        Result::query()->create([
            'course_id' => 2,
            'course_status_id' => 1,
            'credit_unit_id' => 1,
            'data' => '1 1 2 1 45 D 2',
            'enrollment_id' => 1,
            'grade' => 'D',
            'grade_point' => 2,
            'scores' => json_encode(['course-work' => 18, 'exam' => 27]),
            'semester_id' => 1,
            'total_score' => 45,
        ]);

        //CHM 101
        Result::query()->create([
            'course_id' => 3,
            'course_status_id' => 1,
            'credit_unit_id' => 3,
            'data' => '1 1 2 3 40 E 9',
            'enrollment_id' => 1,
            'grade' => 'E',
            'grade_point' => 9,
            'scores' => json_encode(['course-work' => 20, 'exam' => 20]),
            'semester_id' => 1,
            'total_score' => 40,
        ]);

        //CHM 107
        Result::query()->create([
            'course_id' => 4,
            'course_status_id' => 1,
            'credit_unit_id' => 3,
            'data' => '1 1 2 3 30 F 0',
            'enrollment_id' => 1,
            'grade' => 'F',
            'grade_point' => 0,
            'scores' => json_encode(['course-work' => 10, 'exam' => 20]),
            'semester_id' => 1,
            'total_score' => 30,
        ]);

        //CSC 101
        Result::query()->create([
            'course_id' => 5,
            'course_status_id' => 1,
            'credit_unit_id' => 3,
            'data' => '1 1 2 3 70 A 15',
            'enrollment_id' => 1,
            'grade' => 'A',
            'grade_point' => 15,
            'scores' => json_encode(['course-work' => 20, 'exam' => 60]),
            'semester_id' => 1,
            'total_score' => 70,
        ]);
    }
}
