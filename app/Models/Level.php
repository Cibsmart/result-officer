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
            Cache::remember("level_name.{$levelName}",
                now()->addDay(),
                fn () => self::query()->where('name', $levelName)->firstOrFail());
    }

    public static function getUsingId(int $levelId): self
    {
        return
            Cache::remember("level_id.{$levelId}",
                now()->addDay(),
                fn () => self::query()->where('id', $levelId)->firstOrFail());
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }
}
