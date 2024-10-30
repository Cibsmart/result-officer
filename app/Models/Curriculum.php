<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

final class Curriculum extends Model
{
    public static function getUsingCode(string $curriculumCode): self
    {
        return self::query()->where('code', $curriculumCode)->firstOrFail();
    }
}
