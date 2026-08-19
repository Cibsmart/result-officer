<?php

declare(strict_types=1);

namespace App\Rules;

use App\Enums\Permission;
use App\Models\User;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/**
 * Confirms the submitted department is one the current user may exercise the
 * given capability against.
 *
 * Department scoping used to be presentational only: DepartmentListData::forUser()
 * narrowed the dropdown, but every request class validated the submitted id with
 * `exists:departments,id` and nothing more. An officer scoped to one department
 * could edit a single form field and pull another department's full result set.
 *
 * The permission decides the reach. A capability held institution-wide passes for
 * any department — which is what lets the desk and database officers read across
 * the institution while still being scoped when they act.
 *
 * The permission is normally left unset and resolved from the `permission:`
 * middleware the route already declares, so the route stays the single source of
 * truth. That also lets one request class serve several routes: the shared
 * DepartmentSessionRequest backs both an export and three portal downloads, which
 * are different capabilities with different reach.
 */
final readonly class AccessibleDepartment implements ValidationRule
{
    public function __construct(private ?Permission $permission = null)
    {
    }

    /** @param \Closure(string, string|null=): \Illuminate\Translation\PotentiallyTranslatedString $fail */
    public function validate(
        string $attribute,
        mixed $value,
        Closure $fail,
    ): void {
        // phpcs:ignore SlevomatCodingStandard.Functions.UnusedParameter
        unset($attribute);

        $user = Auth::user();

        if (! $user instanceof User) {
            $fail('You are not authorised to access this department.');

            return;
        }

        if ($user->canAccessDepartment((int) $value, $this->permission())) {
            return;
        }

        $fail('You are not authorised to access this department.');
    }

    private static function permissionDeclaredByCurrentRoute(): ?Permission
    {
        $route = Route::current();

        if ($route === null) {
            return null;
        }

        foreach ($route->gatherMiddleware() as $middleware) {
            if (is_string($middleware) && str_starts_with($middleware, 'permission:')) {
                return Permission::tryFrom(mb_substr($middleware, mb_strlen('permission:')));
            }
        }

        return null;
    }

    /** The capability being exercised: the one given, else the route's own. */
    private function permission(): ?Permission
    {
        return $this->permission instanceof Permission
            ? $this->permission
            : self::permissionDeclaredByCurrentRoute();
    }
}
