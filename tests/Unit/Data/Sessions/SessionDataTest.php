<?php

declare(strict_types=1);

use App\Data\Session\SessionData;
use Tests\Factories\SessionFactory;

test('session dto is correct', function (): void {
    $session = SessionFactory::new()->createOne();

    $data = SessionData::from($session);

    expect($data)->toBeInstanceOf(SessionData::class)
        ->and($data->id)->toBe($session->id)
        ->and($data->name)->toBe($session->name);
});
