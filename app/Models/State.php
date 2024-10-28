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
        $state = self::query()->where('name', $stateName)->first();

        if (is_null($state)) {
            $state = self::query()->where('name', 'OTHERS')->firstOrFail();
        }

        return $state;
    }

    /** @return \Illuminate\Database\Eloquent\Relations\BelongsTo<\App\Models\Country, \App\Models\State> */
    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }
}
