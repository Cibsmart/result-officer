<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

final class Semester extends Model
{
    public static function getUsingName(string $semesterName): self
    {
        return self::query()->where('name', $semesterName)->firstOrFail();
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }
}
