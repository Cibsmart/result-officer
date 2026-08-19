<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Enums\Permission;
use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use InvalidArgumentException;
use Symfony\Component\HttpFoundation\Response;

/**
 * Gates a route on one Permission.
 *
 * Every application route declares one, and RouteAuthorizationTest fails the
 * build if any route does not — so a new route without a declared permission
 * cannot ship open by accident. Before this, 105 of 110 routes sat behind a
 * bare `auth` group that any authenticated account satisfied.
 *
 * This decides *whether* the capability is held. Whether it may be exercised
 * against a particular department is the AccessibleDepartment rule's job.
 */
final class EnsurePermission
{
    /** @param \Closure(\Illuminate\Http\Request): \Symfony\Component\HttpFoundation\Response $next */
    public function handle(
        Request $request,
        Closure $next,
        string $permission,
    ): Response {
        $required = Permission::tryFrom($permission);

        if (! $required instanceof Permission) {
            throw new InvalidArgumentException("{$permission} is not a known permission.");
        }

        $user = $request->user();

        abort_unless(
            $user instanceof User && $user->hasPermission($required),
            403,
            "{$required->label()} is not permitted for your role.",
        );

        return $next($request);
    }
}
