<?php

declare(strict_types=1);

use App\Enums\ClassOfDegree;

test('valid fcgpa return correct class of degree', function (float $fcgpa, ClassOfDegree $class): void {
    expect(ClassOfDegree::for($fcgpa)->value)->toBe($class->value);
})->with([
    [0.00, ClassOfDegree::FAIL],
    [1.00, ClassOfDegree::PASS],
    [1.49, ClassOfDegree::PASS],
    [1.50, ClassOfDegree::THIRD_CLASS],
    [2.49, ClassOfDegree::THIRD_CLASS],
    [2.50, ClassOfDegree::SECOND_CLASS_LOWER],
    [3.49, ClassOfDegree::SECOND_CLASS_LOWER],
    [3.50, ClassOfDegree::SECOND_CLASS_UPPER],
    [4.49, ClassOfDegree::SECOND_CLASS_UPPER],
    [4.50, ClassOfDegree::FIRST_CLASS],
    [5.00, ClassOfDegree::FIRST_CLASS],
]);

test('invalid fcgpa return fail of degree', function (float $fcgpa): void {
    expect(ClassOfDegree::for($fcgpa)->value)->toBe(ClassOfDegree::FAIL->value);
})->with([
    [- 0.01, ClassOfDegree::FAIL],
    [5.01, ClassOfDegree::FAIL],
]);
