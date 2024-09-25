<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

final class EntryMode extends Model
{
    public static function getUsingCode(string $entryModeCode): self
    {
        return self::query()->where('code', $entryModeCode)->firstOrFail();
    }
}
