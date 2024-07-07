<?php

use App\Enums\Grade;
use App\Values\ExamScore;
use App\Values\Grader;
use App\Values\InCourseScore;
use App\Values\Score;

test('grade is correct', function (int $inCourse, int $exam, Grade $grade) {
    $score = Score::new(InCourseScore::new($inCourse), ExamScore::new($exam));
    expect((Grader::new($score))->grade())->toBe($grade);
})->with([
    [0, 0, Grade::F],
    [10, 10, Grade::F],
    [10, 30, Grade::E],
    [15, 30, Grade::D],
    [20, 35, Grade::C],
    [15, 45, Grade::B],
    [30, 70, Grade::A],
]);
