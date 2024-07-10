<?php

declare(strict_types=1);

use App\Values\ExamScore;
use App\Values\InCourseScore;
use App\Values\TotalScore;

test('total score is correct', function (int $inCourse, int $exam, int $total): void {
    expect(TotalScore::fromInCourseAndExam(InCourseScore::new($inCourse), ExamScore::new($exam))->value)->toBe($total)
        ->and(TotalScore::new(InCourseScore::new($inCourse)->value + ExamScore::new($exam)->value)->value)->toBe($total);
})->with([
    [0, 0, 0],
    [10, 10, 20],
    [10, 30, 40],
    [20, 25, 45],
    [15, 35, 50],
    [20, 40, 60],
    [10, 60, 70],
    [30, 70, 100],
]);

test('throws no exception for valid total scores', function (): void {
    TotalScore::new(0);
    TotalScore::new(50);
    TotalScore::new(100);
})->throwsNoExceptions();

test('throws exception for negative total score', function (): void {
    TotalScore::new(-1);
})->throws(InvalidArgumentException::class, 'Total score value must be between 0 and 100');

test('throws exception for total score greater than 100', function (): void {
    TotalScore::new(101);
})->throws(InvalidArgumentException::class, 'Total score value must be between 0 and 100');
