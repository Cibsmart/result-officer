<?php

declare(strict_types=1);

use App\Data\Download\PortalDateData;
use Carbon\Carbon;

it('can normalizer valid portal date', function (): void {
    $date = '2000-01-30';

    $data = PortalDateData::from($date);

    expect($data)->toBeInstanceOf(PortalDateData::class)
        ->and($data->value)->toBeInstanceOf(Carbon::class)
        ->and($data->value->format('Y-m-d'))->toBe($date);
});

it('can normalizer valid transposed portal date', function (): void {
    $date = '30-12-2000';

    $data = PortalDateData::from($date);

    expect($data)->toBeInstanceOf(PortalDateData::class)
        ->and($data->value)->toBeInstanceOf(Carbon::class)
        ->and($data->value->format('d-m-Y'))->toBe($date);
});

it('can normalizer invalid portal date to null', function ($date): void {
    $data = PortalDateData::from($date);

    expect($data)->toBeInstanceOf(PortalDateData::class)
        ->and($data->value)->toBeNull();
})->with([
    [''],
    ['30-12-200'],
    ['30-30-2001'],
    ['2001-30-30'],
    ['2001-12-40'],
]);
