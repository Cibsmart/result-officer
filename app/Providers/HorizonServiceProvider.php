<?php

declare(strict_types=1);

namespace App\Providers;

use App\Enums\Role;
use Illuminate\Support\Facades\Gate;
use Laravel\Horizon\HorizonApplicationServiceProvider;

final class HorizonServiceProvider extends HorizonApplicationServiceProvider
{
    protected function gate(): void
    {
        Gate::define('viewHorizon', fn ($user) => in_array($user->role, [Role::SUPER_ADMIN, Role::ADMIN], true));
    }
}
