<?php

declare(strict_types=1);

namespace App\Policies;

use App\Enums\ImportEventStatus;
use App\Models\ExcelImportEvent;
use App\Models\User;

final class ExcelImportEventPolicy
{
    public function view(User $user, ExcelImportEvent $excelImportEvent): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        return $user->id === $excelImportEvent->user_id;
    }

    public function update(User $user, ExcelImportEvent $excelImportEvent): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        return $user->id === $excelImportEvent->user_id;
    }

    public function delete(User $user, ExcelImportEvent $excelImportEvent): bool
    {
        if ($excelImportEvent->status === ImportEventStatus::COMPLETED) {
            return false;
        }

        if ($user->isAdmin()) {
            return true;
        }

        return $user->id === $excelImportEvent->user_id;
    }
}
