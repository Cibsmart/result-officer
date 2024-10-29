<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\CourseStatus;
use App\Enums\Grade;
use App\Models\Registration;
use App\Models\Result;
use App\Values\ExamScore;
use App\Values\InCourseScore;
use App\Values\TotalScore;
use Illuminate\Database\Seeder;

final class ResultSeeder extends Seeder
{
    // semester_enrollment_id => [course_id, course_status_id, credit_unit, scores]
    private array $results = [
        1 => [
            [1, 'F', 2, [15, 35]],
            [2, 'F', 2, [18, 27]],
            [3, 'F', 2, [20, 20]],
            [4, 'F', 3, [10, 20]],
            [5, 'F', 2, [15, 29]],
            [6, 'F', 3, [20, 40]],
            [7, 'F', 1, [25, 50]],
            [8, 'F', 1, [10, 49]],
            [9, 'F', 3, [30, 60]],
            [10, 'F', 3, [30, 70]],
            [11, 'F', 1, [20, 20]],
        ],
        // EBSU/2009/51486 - First Year First Semester
        2 => [
            [1, 'F', 2, [10, 60]],
            [2, 'F', 2, [10, 30]],
            [3, 'F', 2, [20, 60]],
            [4, 'F', 3, [10, 50]],
            [5, 'F', 2, [15, 60]],
            [6, 'F', 3, [20, 30]],
            [7, 'F', 1, [25, 30]],
            [8, 'F', 1, [10, 60]],
            [9, 'F', 3, [30, 30]],
            [10, 'F', 3, [30, 35]],
            [11, 'F', 1, [20, 40]],
        ],
        // EBSU/2009/51486 - First Year Second Semester
        3 => [
            [12, 'F', 2, [10, 60]],
            [13, 'F', 2, [10, 50]],
            [14, 'F', 3, [20, 30]],
            [15, 'F', 1, [10, 60]],
            [16, 'F', 3, [15, 25]],
            [17, 'F', 3, [20, 40]],
            [18, 'F', 2, [25, 55]],
            [19, 'F', 2, [10, 60]],
            [20, 'F', 2, [0, 0]],
            [21, 'F', 1, [30, 35]],
            [22, 'F', 2, [20, 40]],
        ],
        // EBSU/2009/51486 - Second Year First Semester
        4 => [
            [23, 'F', 3, [10, 60]],
            [24, 'F', 2, [10, 60]],
            [25, 'F', 2, [20, 50]],
            [26, 'F', 2, [10, 60]],
            [27, 'F', 3, [15, 60]],
            [28, 'F', 3, [20, 40]],
            [29, 'F', 2, [25, 55]],
            [30, 'F', 3, [10, 60]],
            [31, 'F', 2, [20, 30]],
        ],
        // EBSU/2009/51486 - Second Year Second Semester
        5 => [
            [32, 'F', 2, [10, 60]],
            [33, 'F', 2, [10, 60]],
            [34, 'F', 2, [20, 50]],
            [35, 'F', 2, [10, 60]],
            [36, 'F', 2, [15, 45]],
            [37, 'F', 2, [20, 40]],
            [38, 'F', 2, [25, 25]],
            [39, 'F', 3, [10, 50]],
            [40, 'F', 2, [10, 40]],
            [29, 'R', 2, [30, 30]],
        ],
        // EBSU/2009/51486 - Third Year First Semester
        6 => [
            [41, 'F', 2, [20, 50]],
            [42, 'F', 2, [20, 50]],
            [43, 'F', 2, [20, 50]],
            [44, 'F', 2, [20, 50]],
            [45, 'F', 2, [20, 50]],
            [46, 'F', 2, [20, 40]],
            [47, 'F', 2, [20, 50]],
            [48, 'F', 2, [20, 50]],
            [49, 'F', 2, [20, 30]],
            [50, 'F', 2, [20, 50]],
            [51, 'F', 2, [20, 50]],
        ],
        // EBSU/2009/51486 - Third Year Second Semester
        7 => [
            [52, 'F', 18, [10, 60]],
        ],
        // EBSU/2009/51486 - Fourth Year First Semester
        8 => [
            [53, 'F', 2, [20, 30]],
            [54, 'F', 2, [20, 50]],
            [55, 'F', 2, [20, 50]],
            [56, 'F', 2, [20, 50]],
            [57, 'F', 1, [20, 30]],
            [58, 'F', 2, [20, 30]],
            [59, 'F', 2, [20, 50]],
            [60, 'F', 2, [20, 50]],
            [61, 'F', 2, [20, 50]],
            [62, 'F', 1, [20, 50]],
            [63, 'F', 2, [20, 50]],
            [64, 'F', 2, [20, 30]],
        ],
        // EBSU/2009/51486 - Fourth Year Second Semester
        9 => [
            [65, 'F', 2, [20, 30]],
            [66, 'F', 2, [20, 50]],
            [67, 'F', 2, [20, 50]],
            [68, 'F', 2, [20, 30]],
            [69, 'F', 2, [20, 50]],
            [70, 'F', 2, [20, 30]],
            [71, 'F', 6, [20, 50]],
            [72, 'F', 2, [20, 50]],
            [73, 'F', 1, [20, 50]],
        ],
        // EBSU/2009/51895 - First Year First Semester
        10 => [
            [1, 'F', 2, [10, 60]],
            [2, 'F', 2, [10, 30]],
            [3, 'F', 2, [20, 60]],
            [4, 'F', 3, [10, 50]],
            [5, 'F', 2, [15, 60]],
            [6, 'F', 3, [20, 30]],
            [7, 'F', 1, [25, 30]],
            [8, 'F', 1, [10, 60]],
            [9, 'F', 3, [30, 30]],
            [10, 'F', 3, [30, 35]],
            [11, 'F', 1, [20, 40]],
        ],
        // EBSU/2009/51895 - First Year Second Semester
        11 => [
            [12, 'F', 2, [10, 60]],
            [13, 'F', 2, [10, 50]],
            [14, 'F', 3, [20, 30]],
            [15, 'F', 1, [10, 60]],
            [16, 'F', 3, [15, 25]],
            [17, 'F', 3, [20, 40]],
            [18, 'F', 2, [25, 55]],
            [19, 'F', 2, [10, 60]],
            [20, 'F', 2, [20, 40]],
            [21, 'F', 1, [30, 35]],
            [22, 'F', 2, [20, 40]],
        ],
    ];

    public function run(): void
    {
        foreach ($this->results as $semester_enrollment => $results) {
            foreach ($results as $result) {
                $registration = Registration::query()->create([
                    'course_id' => $result[0],
                    'course_status' => CourseStatus::from($result[1])->value,
                    'credit_unit' => $result[2],
                    'semester_enrollment_id' => $semester_enrollment,
                ]);

                $score = TotalScore::fromInCourseAndExam(InCourseScore::new($result[3][0]),
                    ExamScore::new($result[3][1]));
                $grade = Grade::for($score);
                $gradePoint = $grade->point() * $result[2];
                $data = "$registration->id $score->value {$grade->value} $gradePoint";

                Result::query()->create([
                    'data' => $data,
                    'grade' => $grade->name,
                    'grade_point' => $gradePoint,
                    'registration_id' => $registration->id,
                    'scores' => json_encode(['in-course' => $result[3][0], 'exam' => $result[3][1]]),
                    'total_score' => $score->value,
                ]);
            }
        }
    }
}
