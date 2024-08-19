<?php

declare(strict_types=1);

namespace App\Providers;

use App\Enums\NotificationType;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;

final class InertiaServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        RedirectResponse::macro(name: 'flash',
            macro: function (NotificationType $type, string $body) {
                $body = Str::of($body)->limit(1000)->value();

                session()->flash('notification', ['type' => $type, 'body' => $body]);

                return $this;
            },
        );

        RedirectResponse::macro(name: 'success',
            macro: fn (string $body): RedirectResponse => $this->flash(NotificationType::SUCCESS, $body));

        RedirectResponse::macro(name: 'error',
            macro: fn (string $body): RedirectResponse => $this->flash(NotificationType::ERROR, $body));

        RedirectResponse::macro(name: 'warning',
            macro: fn (string $body): RedirectResponse => $this->flash(NotificationType::WARNING, $body));

        RedirectResponse::macro(name: 'info',
            macro: fn (string $body): RedirectResponse => $this->flash(NotificationType::INFO, $body));
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
    }
}
