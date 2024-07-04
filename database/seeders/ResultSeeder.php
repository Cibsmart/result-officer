<?php

namespace Database\Seeders;

use App\Models\Result;
use Illuminate\Database\Seeder;

class ResultSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //BIO 101
        Result::query()->create([
            'enrollment_id' => 1,
            'semester_id' => 1,
            'course_id' => 1,
            'credit_unit_id' => 3,
            'course_status_id' => 1,
            'scores' => json_encode(['course-work' => 15, 'exam' => 35]),
            'total_score' => 50,
            'grade' => 'C',
            'grade_point' => 9,
            'data' => '1 1 1 3 50 C 9',
        ]);

        //BIO 107
        Result::query()->create([
            'enrollment_id' => 1,
            'semester_id' => 1,
            'course_id' => 2,
            'credit_unit_id' => 1,
            'course_status_id' => 1,
            'scores' => json_encode(['course-work' => 18, 'exam' => 27]),
            'total_score' => 45,
            'grade' => 'D',
            'grade_point' => 2,
            'data' => '1 1 2 1 45 D 2',
        ]);

        //CHM 101
        Result::query()->create([
            'enrollment_id' => 1,
            'semester_id' => 1,
            'course_id' => 3,
            'credit_unit_id' => 3,
            'course_status_id' => 1,
            'scores' => json_encode(['course-work' => 20, 'exam' => 20]),
            'total_score' => 40,
            'grade' => 'E',
            'grade_point' => 9,
            'data' => '1 1 2 3 40 E 9',
        ]);

        //CHM 107
        Result::query()->create([
            'enrollment_id' => 1,
            'semester_id' => 1,
            'course_id' => 4,
            'credit_unit_id' => 3,
            'course_status_id' => 1,
            'scores' => json_encode(['course-work' => 10, 'exam' => 20]),
            'total_score' => 30,
            'grade' => 'F',
            'grade_point' => 0,
            'data' => '1 1 2 3 30 F 0',
        ]);

        //CSC 101
        Result::query()->create([
            'enrollment_id' => 1,
            'semester_id' => 1,
            'course_id' => 5,
            'credit_unit_id' => 3,
            'course_status_id' => 1,
            'scores' => json_encode(['course-work' => 20, 'exam' => 60]),
            'total_score' => 70,
            'grade' => 'A',
            'grade_point' => 15,
            'data' => '1 1 2 3 70 A 15',
        ]);
    }
}
