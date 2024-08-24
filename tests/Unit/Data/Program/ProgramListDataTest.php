<?php

declare(strict_types=1);

use App\Data\Program\ProgramListData;
use Tests\Factories\ProgramFactory;

test('program list dto is correct', function (): void {
    $count = 4;

    ProgramFactory::new()->count($count)->active()->create();

    $data = ProgramListData::new();

    expect($data)->toBeInstanceOf(ProgramListData::class)
        ->and($data->programs->count())->toEqual($count + 1);
});
