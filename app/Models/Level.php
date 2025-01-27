<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

final class Level extends Model
{
    public static function getUsingName(string $levelName): self
    {
        return
            Cache::remember("level_{$levelName}",
                now()->addDay(),
                fn () => self::query()->where('name', $levelName)->firstOrFail());
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }
}
