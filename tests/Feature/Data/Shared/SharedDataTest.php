<?php

declare(strict_types=1);

use App\Data\Shared\NotificationData;
use App\Data\Shared\SharedData;
use App\Enums\NotificationType;

test('shared data is correct', function (): void {
    $notification = ['type' => NotificationType::INFO, 'body' => 'success'];

    session()->flash('notification', $notification);

    $data = new SharedData();

    expect($data)->toBeInstanceOf(SharedData::class)
        ->and($data->notification)->toBeInstanceOf(NotificationData::class);
});
