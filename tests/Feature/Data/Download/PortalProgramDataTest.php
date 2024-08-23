<?php

declare(strict_types=1);

use App\Data\Download\PortalProgramData;

test('portal program data is correct', function (): void {
    $program = ['id' => '1', 'name' => 'Program'];

    $data = PortalProgramData::from($program);

    expect($data)->toBeInstanceOf(PortalProgramData::class)
        ->and($data->id)->toBe($program['id'])
        ->and($data->name)->toBe($program['name']);
});
