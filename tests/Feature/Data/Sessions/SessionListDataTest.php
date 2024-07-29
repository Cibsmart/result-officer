<?php

declare(strict_types=1);

use App\Data\Session\SessionListData;
use Tests\Factories\SessionFactory;

test('session list dto is correct', function (): void {
    $count = 4;

    SessionFactory::new()->count($count)->create();

    $data = SessionListData::new();

    expect($data)->toBeInstanceOf(SessionListData::class)
        ->and($data->sessions->count())->toEqual($count);
});
