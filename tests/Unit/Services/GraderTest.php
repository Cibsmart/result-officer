<?php

declare(strict_types=1);

use App\Enums\Grade;
use App\Services\Grader;
use App\Values\ExamScore;
use App\Values\InCourseScore;
use App\Values\TotalScore;

test('grade is correct', function (int $inCourse, int $exam, Grade $grade, bool $isEGradeAllowed): void {
    $score = TotalScore::new(InCourseScore::new($inCourse)->value + ExamScore::new($exam)->value);
    expect(Grader::new($score, $isEGradeAllowed)->grade())->toBe($grade);
})->with([
    [0, 0, Grade::F, true],
    [10, 10, Grade::F, true],
    [10, 34, Grade::F, false],
    [10, 30, Grade::E, true],
    [15, 30, Grade::D, true],
    [20, 35, Grade::C, true],
    [15, 45, Grade::B, true],
    [30, 70, Grade::A, true],
]);
