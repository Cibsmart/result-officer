<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\User;

final class UserPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isAdmin();
    }

    public function view(User $user, User $model): bool
    {
        if ($model->isSuperAdmin()) {
            return false;
        }

        return $user->isAdmin();
    }

    public function create(User $user): bool
    {
        return $user->isAdmin();
    }

    public function update(User $user, User $model): bool
    {
        if ($model->isSuperAdmin()) {
            return false;
        }

        if (! $user->isAdmin()) {
            return false;
        }

        if ($model->isAdmin() && $user->id !== $model->id) {
            return false;
        }

        return $user->isAdmin();
    }

    public function delete(User $user, User $model): bool
    {
        if ($model->isSuperAdmin()) {
            return false;
        }

        return $user->isSuperAdmin();
    }

    public function restore(User $user): bool
    {
        return $user->isSuperAdmin();
    }

    public function forceDelete(): bool
    {
        return false;
    }
}
