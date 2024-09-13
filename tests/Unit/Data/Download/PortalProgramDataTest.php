<?php

declare(strict_types=1);

use App\Data\Download\PortalProgramData;

test('portal program data is correct', function (): void {
    $program = 'Program';

    $data = PortalProgramData::from($program);

    expect($data)->toBeInstanceOf(PortalProgramData::class)
        ->and($data->name)->toBe($program);
});
