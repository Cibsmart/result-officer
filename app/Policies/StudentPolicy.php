<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

final class StudentPolicy
{
    use HandlesAuthorization;

    public function delete(User $user): bool
    {
        return $user->isAdmin();
    }

    public function restore(User $user): bool
    {
        return $user->isAdmin();
    }

    public function forceDelete(): bool
    {
        return false;
    }
}
