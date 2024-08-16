<?php

declare(strict_types=1);

use App\Values\ExamScore;

test('throws no exception for valid exam scores', function (int $score): void {
    ExamScore::new($score);
})
    ->with([
        [0],
        [50],
        [70],
        [100],
    ])
    ->throwsNoExceptions();

test('throws exception for negative exam score', function (): void {
    ExamScore::new(- 1);
})->throws(InvalidArgumentException::class, 'Exam score value must be between 0 and 100');

test('throws exception for exam score greater than 100', function (): void {
    ExamScore::new(101);
})->throws(InvalidArgumentException::class, 'Exam score value must be between 0 and 100');
