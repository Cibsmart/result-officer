<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\RawDataStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class RawResult extends Model
{
    /** @return \Illuminate\Database\Eloquent\Relations\BelongsTo<\App\Models\ImportEvent, \App\Models\RawResult> */
    public function event(): BelongsTo
    {
        return $this->belongsTo(ImportEvent::class);
    }

    /** @return array<string, string> */
    protected function casts(): array
    {
        return [
            'status' => RawDataStatus::class,
        ];
    }
}
