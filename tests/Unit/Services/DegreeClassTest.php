<?php

declare(strict_types=1);

use App\Enums\DegreeClassEnum;
use App\Services\DegreeClass;

test('valid fcgpa return correct class of degree', function (float $fcgpa, DegreeClassEnum $class): void {
    expect(DegreeClass::for($fcgpa)->value()->value)->toBe($class->value);
})->with([
    [0.00, DegreeClassEnum::FAIL],
    [1.00, DegreeClassEnum::PASS],
    [1.49, DegreeClassEnum::PASS],
    [1.50, DegreeClassEnum::THIRD_CLASS],
    [2.49, DegreeClassEnum::THIRD_CLASS],
    [2.50, DegreeClassEnum::SECOND_CLASS_LOWER],
    [3.49, DegreeClassEnum::SECOND_CLASS_LOWER],
    [3.50, DegreeClassEnum::SECOND_CLASS_UPPER],
    [4.49, DegreeClassEnum::SECOND_CLASS_UPPER],
    [4.50, DegreeClassEnum::FIRST_CLASS],
    [5.00, DegreeClassEnum::FIRST_CLASS],
]);

test('invalid fcgpa return fail of degree', function (float $fcgpa): void {
    expect(DegreeClass::for($fcgpa)->value()->value)->toBe(DegreeClassEnum::FAIL->value);
})->with([
    [- 0.01, DegreeClassEnum::FAIL],
    [5.01, DegreeClassEnum::FAIL],
]);
