<?php

use App\Values\InCourseScore;

test('throws no exception for valid in-course scores', function () {
    new InCourseScore(0);
    new InCourseScore(15);
    new InCourseScore(30);
})->throwsNoExceptions();

test('throws exception for negative in-course score', function () {
    new InCourseScore(-1);
})->throws(InvalidArgumentException::class, 'In-course score value must be between 0 and 30');

test('throws exception for in-course score greater than 30', function () {
    new InCourseScore(71);
})->throws(InvalidArgumentException::class, 'In-course score value must be between 0 and 30');
