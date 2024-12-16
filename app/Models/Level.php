<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

final class Level extends Model
{
    public static function getUsingName(string $levelName): self
    {
        return self::query()->where('name', $levelName)->firstOrFail();
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }
}
