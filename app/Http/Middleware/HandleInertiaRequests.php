<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Data\Shared\SharedData;
use App\Data\Shared\UserData;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Middleware;

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
        $state = new SharedData(
            user: fn () => Auth::check() ? UserData::from(Auth::user()) : null,
        );

        return [
            ...parent::share($request),
            ...$state->toArray(),
        ];
    }
}
