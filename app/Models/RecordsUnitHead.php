<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

final class RecordsUnitHead extends Model
{
    /** @return array<string, string> */
    protected function casts(): array
    {
        return [
            'is_current' => 'boolean',
        ];
    }
}
