<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class LocalGovernment extends Model
{
    protected $fillable = ['state_id', 'name'];

    public static function getUsingName(mixed $localGovernmentName): self
    {
        return self::query()->where('name', $localGovernmentName)->firstOrFail();
    }

    /** @return \Illuminate\Database\Eloquent\Relations\BelongsTo<\App\Models\State, \App\Models\Student> */
    public function state(): BelongsTo
    {
        return $this->belongsTo(State::class);
    }
}
