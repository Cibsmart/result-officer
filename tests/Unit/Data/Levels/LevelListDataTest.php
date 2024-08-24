<?php

declare(strict_types=1);

use App\Data\Level\LevelListData;
use Tests\Factories\LevelFactory;

test('level list dto is correct', function (): void {
    $count = 4;

    LevelFactory::new()->count($count)->create();

    $data = LevelListData::new();

    expect($data)->toBeInstanceOf(LevelListData::class)
        ->and($data->levels->count())->toEqual($count + 1);
});
