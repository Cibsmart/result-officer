<?php

declare(strict_types=1);

namespace App\Data\Shared;

use Closure;
use Spatie\LaravelData\Data;
use Spatie\TypeScriptTransformer\Attributes\TypeScriptType;

final class SharedData extends Data
{
    public function __construct(
        public readonly string $name,
        public readonly QuoteData $quote,
        public readonly string $location,
        public readonly bool $sidebarOpen,
        #[TypeScriptType(UserData::class)]
        public ?Closure $user = null,
        public ?NotificationData $notification = null,
    ) {
        $this->shareNotification();
    }

    private function shareNotification(): void
    {
        if (! session('notification')) {
            return;
        }

        /** @var array{type: \App\Enums\NotificationType, body: string} $notification */
        $notification = session('notification');

        $this->notification = new NotificationData(...$notification);
    }
}
