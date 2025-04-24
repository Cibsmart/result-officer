<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class LocalGovernment extends Model
{
    public static function getUsingName(string $localGovernmentName): self
    {
        $lga = self::query()->where('name', $localGovernmentName)->first();

        if (is_null($lga)) {
            $lga = self::query()->where('name', 'OTHERS')->firstOrFail();
        }

        return $lga;
    }

    public static function getUsingId(int $localGovernmentId): self
    {
        return self::query()->where('id', $localGovernmentId)->firstOrFail();
    }

    /**
     * @phpstan-return \Illuminate\Database\Eloquent\Relations\BelongsTo<\App\Models\State, $this>
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<\App\Models\State, static>
     */
    public function state(): BelongsTo
    {
        return $this->belongsTo(State::class);
    }
}
