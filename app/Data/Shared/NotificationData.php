<?php

declare(strict_types=1);

namespace App\Data\Shared;

use App\Enums\NotificationType;
use Spatie\LaravelData\Data;

final class NotificationData extends Data
{
    public function __construct(
        public NotificationType $type,
        public string $body,
    ) {
    }
}
