<?php

declare(strict_types=1);

use App\Values\SessionValue;

it('creates valid session value', function (): void {
    $stringSession = '2009/2010';

    $session = SessionValue::new($stringSession);

    expect($session)->toBeInstanceOf(SessionValue::class)
        ->and($session->value)->toBeString()
        ->and($session->value)->toBe('2009/2010');
});

it('throws exception for invalid session value', function ($stringSession): void {
    SessionValue::new($stringSession);
})
    ->with([
        [''],
        ['200/2010'],
        ['2009/201'],
        ['20001/2010'],
        ['20092010'],
        ['2009/2011'],
    ])
    ->throws(InvalidArgumentException::class);

it('extract the first year from session', function (): void {
    $session = SessionValue::new('2009/2010');

    expect($session->firstYear())->toBeInt()->toBe(2009);
});

it('extract the last year from session', function (): void {
    $session = SessionValue::new('2009/2010');

    expect($session->lastYear())->toBeInt()->toBe(2010);
});
