<?php

declare(strict_types=1);

use App\Values\InCourseScore;

test('throws no exception for valid in-course scores', function (int $score): void {
    InCourseScore::new($score);
})
    ->with([
        [0],
        [15],
        [30],
        [50],
    ])
    ->throwsNoExceptions();

test('throws exception for negative in-course score', function (): void {
    InCourseScore::new(- 1);
})->throws(InvalidArgumentException::class, 'In-course score value must be between 0 and 50');

test('throws exception for in-course score greater than 30', function (): void {
    InCourseScore::new(101);
})->throws(InvalidArgumentException::class, 'In-course score value must be between 0 and 50');
