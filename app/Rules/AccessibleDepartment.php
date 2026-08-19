<?php

declare(strict_types=1);

namespace App\Rules;

use App\Models\User;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\Auth;

/**
 * Confirms the submitted department is one the current user may act on.
 *
 * Department scoping used to be presentational only: DepartmentListData::forUser()
 * narrowed the dropdown, but every request class validated the submitted id with
 * `exists:departments,id` and nothing more. An officer scoped to one department
 * could edit a single form field and pull another department's full result set —
 * horizontal privilege escalation across roughly twenty download and export
 * endpoints.
 */
final class AccessibleDepartment implements ValidationRule
{
    /** @param Closure(string, string|null=): \Illuminate\Translation\PotentiallyTranslatedString $fail */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $user = Auth::user();

        if (! $user instanceof User) {
            $fail('You are not authorised to access this department.');

            return;
        }

        if ($user->canAccessDepartment((int) $value)) {
            return;
        }

        $fail('You are not authorised to access this department.');
    }
}
