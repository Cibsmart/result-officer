<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Result;
use App\Values\ExamScore;
use App\Values\Grader;
use App\Values\InCourseScore;
use App\Values\Score;
use Illuminate\Database\Seeder;

final class ResultSeeder extends Seeder
{
    // enrollment_id => [semester_id => [course_id, course_status_id, cu, scores]]
    private array $results = [
        1 => [
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
        ],
        2 => [
            1 => [
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
            2 => [
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
        ],
    ];

    public function run(): void
    {
        foreach ($this->results as $enrollment => $semesters) {
            foreach ($semesters as $semester => $results) {
                foreach ($results as $course) {
                    $score = new Score(InCourseScore::new($course[3][0]), ExamScore::new($course[3][1]));
                    $grader = new Grader($score);
                    $gradePoint = $grader->grade()->value * $course[2];
                    $data = "$enrollment $semester {$score->value()} {$grader->grade()->name} $gradePoint";

                    Result::query()->create([
                        'course_id' => $course[0],
                        'course_status_id' => $course[1],
                        'credit_unit_id' => $course[2],
                        'enrollment_id' => $enrollment,
                        'grade' => $grader->grade()->name,
                        'grade_point' => $gradePoint,
                        'scores' => json_encode($course[3]),
                        'semester_id' => $semester,
                        'total_score' => $score->value(),
                        'data' => $data,
                    ]);
                }
            }
        }

    }
}
