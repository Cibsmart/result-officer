<?php

declare(strict_types=1);

use App\Values\ExamScore;

test('throws no exception for valid exam scores', function (): void {
    ExamScore::new(0);
    ExamScore::new(50);
    ExamScore::new(70);
})->throwsNoExceptions();

test('throws exception for negative exam score', function (): void {
    ExamScore::new(- 1);
})->throws(InvalidArgumentException::class, 'Exam score value must be between 0 and 70');

test('throws exception for exam score greater than 70', function (): void {
    ExamScore::new(71);
})->throws(InvalidArgumentException::class, 'Exam score value must be between 0 and 70');
