<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\CourseRegistration;
use App\Models\Result;
use App\Services\Grader;
use App\Values\ExamScore;
use App\Values\InCourseScore;
use App\Values\TotalScore;
use Illuminate\Database\Seeder;

final class ResultSeeder extends Seeder
{
    // semester_enrollment_id => [course_id, course_status_id, credit_unit, scores]
    private array $results = [
        1 => [
            [1, 1, 2, [15, 35]],
            [2, 1, 2, [18, 27]],
            [3, 1, 2, [20, 20]],
            [4, 1, 3, [10, 20]],
            [5, 1, 2, [15, 29]],
            [6, 1, 3, [20, 40]],
            [7, 1, 1, [25, 50]],
            [8, 1, 1, [10, 49]],
            [9, 1, 3, [30, 60]],
            [10, 1, 3, [30, 70]],
            [11, 1, 1, [20, 20]],
        ],
        // EBSU/2009/51486 - First Year First Semester
        2 => [
            [1, 1, 2, [10, 60]],
            [2, 1, 2, [10, 30]],
            [3, 1, 2, [20, 60]],
            [4, 1, 3, [10, 50]],
            [5, 1, 2, [15, 60]],
            [6, 1, 3, [20, 30]],
            [7, 1, 1, [25, 30]],
            [8, 1, 1, [10, 60]],
            [9, 1, 3, [30, 30]],
            [10, 1, 3, [30, 35]],
            [11, 1, 1, [20, 40]],
        ],
        // EBSU/2009/51486 - First Year Second Semester
        3 => [
            [12, 1, 2, [10, 60]],
            [13, 1, 2, [10, 50]],
            [14, 1, 3, [20, 30]],
            [15, 1, 1, [10, 60]],
            [16, 1, 3, [15, 25]],
            [17, 1, 3, [20, 40]],
            [18, 1, 2, [25, 55]],
            [19, 1, 2, [10, 60]],
            [20, 1, 2, [0, 0]],
            [21, 1, 1, [30, 35]],
            [22, 1, 2, [20, 40]],
        ],
        // EBSU/2009/51486 - Second Year First Semester
        4 => [
            [23, 1, 3, [10, 60]],
            [24, 1, 2, [10, 60]],
            [25, 1, 2, [20, 50]],
            [26, 1, 2, [10, 60]],
            [27, 1, 3, [15, 60]],
            [28, 1, 3, [20, 40]],
            [29, 1, 2, [25, 55]],
            [30, 1, 3, [10, 60]],
            [31, 1, 2, [20, 30]],
        ],
        // EBSU/2009/51486 - Second Year Second Semester
        5 => [
            [32, 1, 2, [10, 60]],
            [33, 1, 2, [10, 60]],
            [34, 1, 2, [20, 50]],
            [35, 1, 2, [10, 60]],
            [36, 1, 2, [15, 45]],
            [37, 1, 2, [20, 40]],
            [38, 1, 2, [25, 25]],
            [39, 1, 3, [10, 50]],
            [40, 1, 2, [10, 40]],
            [29, 2, 2, [30, 30]],
        ],
        // EBSU/2009/51486 - Third Year First Semester
        6 => [
            [41, 1, 2, [20, 50]],
            [42, 1, 2, [20, 50]],
            [43, 1, 2, [20, 50]],
            [44, 1, 2, [20, 50]],
            [45, 1, 2, [20, 50]],
            [46, 1, 2, [20, 40]],
            [47, 1, 2, [20, 50]],
            [48, 1, 2, [20, 50]],
            [49, 1, 2, [20, 30]],
            [50, 1, 2, [20, 50]],
            [51, 1, 2, [20, 50]],
        ],
        // EBSU/2009/51486 - Third Year Second Semester
        7 => [
            [52, 1, 18, [10, 60]],
        ],
        // EBSU/2009/51486 - Fourth Year First Semester
        8 => [
            [53, 1, 2, [20, 30]],
            [54, 1, 2, [20, 50]],
            [55, 1, 2, [20, 50]],
            [56, 1, 2, [20, 50]],
            [57, 1, 1, [20, 30]],
            [58, 1, 2, [20, 30]],
            [59, 1, 2, [20, 50]],
            [60, 1, 2, [20, 50]],
            [61, 1, 2, [20, 50]],
            [62, 1, 1, [20, 50]],
            [63, 1, 2, [20, 50]],
            [64, 1, 2, [20, 30]],
        ],
        // EBSU/2009/51486 - Fourth Year Second Semester
        9 => [
            [65, 1, 2, [20, 30]],
            [66, 1, 2, [20, 50]],
            [67, 1, 2, [20, 50]],
            [68, 1, 2, [20, 30]],
            [69, 1, 2, [20, 50]],
            [70, 1, 2, [20, 30]],
            [71, 1, 6, [20, 50]],
            [72, 1, 2, [20, 50]],
            [73, 1, 1, [20, 50]],
        ],
        // EBSU/2009/51895 - First Year First Semester
        10 => [
            [1, 1, 2, [10, 60]],
            [2, 1, 2, [10, 30]],
            [3, 1, 2, [20, 60]],
            [4, 1, 3, [10, 50]],
            [5, 1, 2, [15, 60]],
            [6, 1, 3, [20, 30]],
            [7, 1, 1, [25, 30]],
            [8, 1, 1, [10, 60]],
            [9, 1, 3, [30, 30]],
            [10, 1, 3, [30, 35]],
            [11, 1, 1, [20, 40]],
        ],
        // EBSU/2009/51895 - First Year Second Semester
        11 => [
            [12, 1, 2, [10, 60]],
            [13, 1, 2, [10, 50]],
            [14, 1, 3, [20, 30]],
            [15, 1, 1, [10, 60]],
            [16, 1, 3, [15, 25]],
            [17, 1, 3, [20, 40]],
            [18, 1, 2, [25, 55]],
            [19, 1, 2, [10, 60]],
            [20, 1, 2, [20, 40]],
            [21, 1, 1, [30, 35]],
            [22, 1, 2, [20, 40]],
        ],
    ];

    public function run(): void
    {
        foreach ($this->results as $semester_enrollment => $results) {
            foreach ($results as $result) {
                $courseRegistration = CourseRegistration::query()->create([
                    'course_id' => $result[0],
                    'course_status_id' => $result[1],
                    'credit_unit' => $result[2],
                    'semester_enrollment_id' => $semester_enrollment,
                ]);

                $score = TotalScore::fromInCourseAndExam(InCourseScore::new($result[3][0]),
                    ExamScore::new($result[3][1]));
                $grader = new Grader($score, true);
                $gradePoint = $grader->grade()->value * $result[2];
                $data = "$courseRegistration->id $score->value {$grader->grade()->name} $gradePoint";

                Result::query()->create([
                    'course_registration_id' => $courseRegistration->id,
                    'data' => $data,
                    'grade' => $grader->grade()->name,
                    'grade_point' => $gradePoint,
                    'scores' => json_encode(['in-course' => $result[3][0], 'exam' => $result[3][1]]),
                    'total_score' => $score->value,
                ]);
            }
        }
    }
}
