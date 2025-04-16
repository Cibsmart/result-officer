<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Data\Shared\QuoteData;
use App\Data\Shared\SharedData;
use App\Data\Shared\UserData;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Middleware;

use function assert;

final class HandleInertiaRequests extends Middleware
{
    /** @var string */
    protected $rootView = 'app';

    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * phpcs:ignore SlevomatCodingStandard.TypeHints.DisallowMixedTypeHint
     * @return array<string, mixed>
     * */
    public function share(Request $request): array
    {
        $user = Auth::user();
        assert($user instanceof User || $user === null);

        $state = new SharedData(
            quote: QuoteData::new(),
            sidebarOpen: ! $request->hasCookie('sidebar_state') || $request->cookie('sidebar_state') === 'true',
            user: fn () => $user ? UserData::fromModel($user) : null,
        );

        return [
            ...parent::share($request),
            ...$state->toArray(),
        ];
    }
}
