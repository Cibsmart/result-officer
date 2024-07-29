<?php

declare(strict_types=1);

use App\Data\Level\LevelData;
use Tests\Factories\LevelFactory;

test('level dto is correct', function (): void {
    $level = LevelFactory::new()->createOne();

    $data = LevelData::from($level);

    expect($data)->toBeInstanceOf(LevelData::class)
        ->and($data->id)->toBe($level->id)
        ->and($data->name)->toBe($level->name);
});
