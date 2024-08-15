<?php

declare(strict_types=1);

use App\Helpers\ComputeAverage;

test('computed average value for integers are correct', function (int $dividend, int $divisor, float $quotient): void {
    expect(ComputeAverage::new($dividend, $divisor)->value())->toBe($quotient);
})->with([
    [99, 22, 4.500],
    [93, 21, 4.429],
    [93, 21, 4.429],
]);

test('computed average value for floats are correct', function (float $dividend, int $divisor, float $quotient): void {
    expect(ComputeAverage::new($dividend, $divisor)->value())->toBe($quotient);
})->with([
    [8.929, 2, 4.465],
    [9.546, 2, 4.773],
]);

test('computed average value for zero divisor is zero', function (int|float $dividend, int $divisor): void {
    expect(ComputeAverage::new($dividend, $divisor)->value())->toBe(0.000);
})->with([
    [0, 0],
    [99, 0],
    [8.295, 0],
]);
