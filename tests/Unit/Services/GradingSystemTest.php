<?php

declare(strict_types=1);

use App\Services\GradingSystem;

test('gets correct grading system', function (string $matriculationNumber, bool $isEGradeAllowed): void {
    expect(GradingSystem::new($matriculationNumber)->isEGradeAllowed())
        ->toBe($isEGradeAllowed);
})->with([
    ['EBSU/2009/51486', true],
    ['EBSU/2012/51486', true],
    ['EBSU/2013/51486', false],
    ['EBSU/2014/51486', false],
    ['EBSU/2015/51486', false],
    ['EBSU/2016/51486', false],
    ['EBSU/2017/51486', false],
    ['EBSU/2018/51486', true],
]);
