<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

final class ResultDetail extends Model
{
    protected $fillable = ['value'];

    /** @var array<int, string> */
    protected $hidden = ['data'];

    /** @return array{data: 'hashed', validate: 'boolean'} */
    protected function casts(): array
    {
        return [
            'data' => 'hashed',
            'validate' => 'boolean',
        ];
    }
}
