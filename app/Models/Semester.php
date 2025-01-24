<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

final class Semester extends Model
{
    public static function getUsingName(string $semesterName): self
    {
        return
            Cache::remember($semesterName,
                now()->addDay(),
                fn () => self::query()->where('name', $semesterName)->firstOrFail());
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }
}
