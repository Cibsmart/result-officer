<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class State extends Model
{
    protected $fillable = ['country_id', 'name'];

    public static function getUsingName(string $stateName): self
    {
        return self::query()->where('name', $stateName)->firstOrFail();
    }

    /** @return \Illuminate\Database\Eloquent\Relations\BelongsTo<\App\Models\Country, \App\Models\State> */
    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }
}
