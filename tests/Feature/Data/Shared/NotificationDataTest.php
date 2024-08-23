<?php

declare(strict_types=1);

use App\Data\Shared\NotificationData;
use App\Enums\NotificationType;

test('notification data is correct', function (): void {

    $data = new NotificationData(NotificationType::SUCCESS, 'success');

    expect($data)->toBeInstanceOf(NotificationData::class)
        ->and($data->type)->toBeInstanceOf(NotificationType::class)
        ->and($data->body)->toBe('success');
});
