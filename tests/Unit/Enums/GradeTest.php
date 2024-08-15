<?php

declare(strict_types=1);

use App\Enums\Grade;
use App\Values\TotalScore;

it('returns correct grade for a given score', function (int $score, Grade $grade, bool $isEGradeAllowed): void {
    $score = TotalScore::new($score);
    expect(Grade::for($score, $isEGradeAllowed))->toBe($grade);
})->with([
    [0, Grade::F, true],
    [10, Grade::F, true],
    [34, Grade::F, false],
    [40, Grade::E, true],
    [45, Grade::D, true],
    [55, Grade::C, true],
    [65, Grade::B, true],
    [70, Grade::A, true],
]);
