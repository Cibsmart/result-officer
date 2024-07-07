<?php

use App\Values\ExamScore;

test('throws no exception for valid exam scores', function () {
    new ExamScore(0);
    new ExamScore(50);
    new ExamScore(70);
})->throwsNoExceptions();

test('throws exception for negative exam score', function () {
    new ExamScore(-1);
})->throws(InvalidArgumentException::class, 'Exam score value must be between 0 and 70');

test('throws exception for exam score greater than 70', function () {
    new ExamScore(71);
})->throws(InvalidArgumentException::class, 'Exam score value must be between 0 and 70');
