<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

final class ProgramType extends Model
{
    public static function getUsingCode(string $programTypeCode): self
    {
        return self::query()->where('code', $programTypeCode)->firstOrFail();
    }
}
