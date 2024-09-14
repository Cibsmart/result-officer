<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\ImportEventType;
use App\Enums\ImportStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

final class ImportEvent extends Model
{
    /** @return \Illuminate\Database\Eloquent\Relations\HasMany<\App\Models\RawResult> */
    public function results(): HasMany
    {
        return $this->hasMany(RawResult::class);
    }

    /** @return array<string, string> */
    protected function casts(): array
    {
        return [
            'data' => 'array',
            'status' => ImportStatus::class,
            'type' => ImportEventType::class,
        ];
    }
}
