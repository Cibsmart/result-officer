<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\CourseType;
use App\Models\Course;
use App\Models\ProgramCurriculumCourse;
use Illuminate\Database\Seeder;

final class ProgramCurriculumCourseSeeder extends Seeder
{
    /** @var array<string, array<string, int>> */
    private array $courses = [
        'BIO 101' => ['type' => CourseType::ELECTIVE, 'unit' => 3, 'curriculum' => 1],
        'BIO 191' => ['type' => CourseType::ELECTIVE, 'unit' => 1, 'curriculum' => 1],
        'CSC 101' => ['type' => CourseType::COMPULSORY, 'unit' => 3, 'curriculum' => 1],
        'GST 101' => ['type' => CourseType::GENERAL, 'unit' => 2, 'curriculum' => 1],
        'GST 103' => ['type' => CourseType::GENERAL, 'unit' => 2, 'curriculum' => 1],
        'ICH 101' => ['type' => CourseType::REQUIRED, 'unit' => 3, 'curriculum' => 1],
        'ICH 191' => ['type' => CourseType::REQUIRED, 'unit' => 1, 'curriculum' => 1],
        'MAT 101' => ['type' => CourseType::REQUIRED, 'unit' => 3, 'curriculum' => 1],
        'PHY 101' => ['type' => CourseType::REQUIRED, 'unit' => 3, 'curriculum' => 1],
        'PHY 191' => ['type' => CourseType::REQUIRED, 'unit' => 1, 'curriculum' => 1],
        'STA 101' => ['type' => CourseType::REQUIRED, 'unit' => 2, 'curriculum' => 1],
    ];

    public function run(): void
    {
        foreach ($this->courses as $code => $course) {
            $courseId = Course::query()->where('code', $code)->first()->id;

            ProgramCurriculumCourse::query()->create([
                'course_id' => $courseId,
                'course_type' => $course['type'],
                'credit_unit' => $course['unit'],
                'program_curriculum_id' => $course['curriculum'],
            ]);
        }
    }
}
