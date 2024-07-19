<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

final class RecordsUnitHead extends Model
{
    /** @var array<int, string> */
    protected $fillable = ['name', 'is_current'];

    /** @return array<string, string> */
    /** @return array<string, string> */
    protected function casts(): array
    {
        return [
            'is_current' => 'boolean',
        ];
    }
}
