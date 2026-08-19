<?php

declare(strict_types=1);

use App\Enums\Permission;
use Illuminate\Routing\Route as RoutingRoute;
use Illuminate\Support\Facades\Route;

/**
 * Deny by default, enforced structurally.
 *
 * Before permissions existed, 105 of 110 application routes sat behind a bare
 * `auth` group, so any authenticated account could read, export and mutate any
 * record in the system. That fix is only durable if a *new* route cannot ship
 * without a capability, which is what this asserts.
 *
 * Add a route and forget the permission and this test fails — that is the point.
 * A route that genuinely needs no capability is self-service or unauthenticated,
 * and belongs in the exempt list with a reason.
 */
const VENDOR_ROUTE_PREFIXES = [
    'filament', 'horizon', 'pulse', 'telescope', 'livewire', 'sanctum',
    'broadcasting', '_ignition', '_debugbar', 'clockwork', '__clockwork',
];

/** Routes acting only on the requester's own account, or needing no account at all. */
const SELF_SERVICE_ROUTES = [
    'login', 'logout', 'register', 'confirm-password',
    'password.confirm', 'password.request', 'password.email', 'password.store',
    'password.reset', 'password.update', 'password.edit',
    'profile.edit', 'profile.update', 'profile.destroy',
    'settings', 'appearance', 'up',
];

function isVendorRoute(RoutingRoute $route): bool
{
    $name = $route->getName() ?? '';

    foreach (VENDOR_ROUTE_PREFIXES as $prefix) {
        if (str_starts_with($name, $prefix) || str_starts_with($route->uri(), $prefix)) {
            return true;
        }
    }

    return false;
}

/** @return list<string> */
function declaredPermissions(RoutingRoute $route): array
{
    $declared = [];

    foreach ($route->gatherMiddleware() as $middleware) {
        if (! is_string($middleware) || ! str_starts_with($middleware, 'permission:')) {
            continue;
        }

        $declared[] = mb_substr($middleware, mb_strlen('permission:'));
    }

    return $declared;
}

/** @return list<\Illuminate\Routing\Route> */
function applicationRoutes(): array
{
    $routes = [];

    foreach (Route::getRoutes() as $route) {
        if (isVendorRoute($route)) {
            continue;
        }

        $routes[] = $route;
    }

    return $routes;
}

function isSelfService(RoutingRoute $route): bool
{
    return in_array($route->getName() ?? '', SELF_SERVICE_ROUTES, true)
        || in_array($route->uri(), SELF_SERVICE_ROUTES, true);
}

function routeLabel(RoutingRoute $route): string
{
    $name = $route->getName() ?? '';

    return $name === ''
        ? $route->uri()
        : $name;
}

it('gates every application route on an explicit permission', function (): void {
    $ungated = [];

    foreach (applicationRoutes() as $route) {
        if (isSelfService($route) || declaredPermissions($route) !== []) {
            continue;
        }

        $ungated[] = routeLabel($route);
    }

    expect($ungated)->toBe([], 'These routes carry no permission and are open to any authenticated account.');
});

/** @return list<string> */
function unknownPermissionsOn(RoutingRoute $route): array
{
    $unknown = [];

    foreach (declaredPermissions($route) as $value) {
        if (Permission::tryFrom($value) !== null) {
            continue;
        }

        $unknown[] = routeLabel($route) . " → {$value}";
    }

    return $unknown;
}

it('names a real permission on every gated route', function (): void {
    $unknown = [];

    foreach (applicationRoutes() as $route) {
        $unknown = [...$unknown, ...unknownPermissionsOn($route)];
    }

    expect($unknown)->toBe([]);
});
