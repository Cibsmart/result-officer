<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\ImportEventStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

final class ExcelImportEvent extends Model
{
    public static function new(
        User $user,
        string $filePath,
        string $fileName,
    ): self {
        $importEvent = new self();

        $importEvent->user_id = $user->id;
        $importEvent->file_name = $fileName;
        $importEvent->file_path = $filePath;
        $importEvent->status = ImportEventStatus::NEW;

        $importEvent->save();

        return $importEvent;
    }

    /** @return \Illuminate\Database\Eloquent\Relations\HasMany<\App\Models\RawFinalResult, \App\Models\ExcelImportEvent> */
    public function rawFinalResults(): HasMany
    {
        return $this->hasMany(RawFinalResult::class);
    }

    /** @return array{status: 'App\Enums\ImportEventStatus' } */
    protected function casts(): array
    {
        return [
            'status' => ImportEventStatus::class,
        ];
    }
}
