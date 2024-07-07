<?php

declare(strict_types=1);

use App\Values\ExamScore;
use App\Values\InCourseScore;
use App\Values\Score;

test('total score is correct', function () {
    expect((new Score(new InCourseScore(10), new ExamScore(50)))->value())->toBe(60)
        ->and((new Score(new InCourseScore(0), new ExamScore(0)))->value())->toBe(00)
        ->and((new Score(new InCourseScore(30), new ExamScore(70)))->value())->toBe(100);
});
