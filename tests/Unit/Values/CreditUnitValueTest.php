<?php

declare(strict_types=1);

use App\Values\CreditUnit;

test('throws no exception for valid credit unit', function ($creditUnit): void {
    CreditUnit::new($creditUnit);
})->throwsNoExceptions()->with([0, 1, 2, 3, 4, 5, 6, 12, 15, 18]);

test('throws exception for negative credit unit', function (): void {
    CreditUnit::new(- 1);
})->throws(InvalidArgumentException::class, 'The credit unit is not a valid credit unit value');

test('throws exception for invalid credit unit', function (): void {
    CreditUnit::new(20);
})->throws(InvalidArgumentException::class, 'The credit unit is not a valid credit unit value');
