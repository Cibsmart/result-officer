<?php

declare(strict_types=1);

use App\Values\DateValue;
use Carbon\Carbon;

it('returns correct carbon date for a string date in Y-m-d format', function (): void {
    $input = '2000-01-30';

    $date = DateValue::fromValue($input);

    expect($date)->toBeInstanceOf(DateValue::class)
        ->and($date->value)->toBeInstanceOf(Carbon::class)
        ->and($date->value->format('Y-m-d'))->toBe($input);
});

it('returns correct carbon date for a string date in d-m-Y format', function (): void {
    $input = '30-12-2000';

    $date = DateValue::fromValue($input);

    expect($date)->toBeInstanceOf(DateValue::class)
        ->and($date->value)->toBeInstanceOf(Carbon::class)
        ->and($date->value->format('d-m-Y'))->toBe($input);
});

it('returns null for an empty string date', function (): void {
    $date = '';

    $data = DateValue::fromValue($date);

    expect($data)->toBeInstanceOf(DateValue::class)
        ->and($data->value)->toBeNull();
});

it('returns null for an invalid date', function (): void {
    $date = '2020-2020-2020';

    $data = DateValue::fromValue($date);

    expect($data)->toBeInstanceOf(DateValue::class)
        ->and($data->value)->toBeNull();
});
