<?php

declare(strict_types=1);

use App\Data\Program\ProgramData;
use Tests\Factories\ProgramFactory;

test('program dto is correct', function (): void {
    $program = ProgramFactory::new()->createOne();

    $data = ProgramData::from($program);

    expect($data)->toBeInstanceOf(ProgramData::class)
        ->and($data->id)->toBe($program->id)
        ->and($data->name)->toBe($program->name);
});
