<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

final class ExcelImportEvent extends Model
{
    /** @return \Illuminate\Database\Eloquent\Relations\HasMany<\App\Models\RawFinalResult, \App\Models\ExcelImportEvent> */
    public function rawFinalResults(): HasMany
    {
        return $this->hasMany(RawFinalResult::class);
    }
}
