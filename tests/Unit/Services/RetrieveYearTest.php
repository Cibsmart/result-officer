<?php

declare(strict_types=1);

use App\Services\RetrieveYear;

test('can retrieve first year from session', function (): void {
    $year = RetrieveYear::fromSession('2009/2010')->firstYear();

    expect($year)->toBe(2009);
});

test('can retrieve last year from session', function (): void {
    $year = RetrieveYear::fromSession('2009/2010')->lastYear();

    expect($year)->toBe(2010);
});
