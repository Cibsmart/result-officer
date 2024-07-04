<?php

namespace App\Models;

use App\Enums\ScoreTypeEnum;
use Illuminate\Database\Eloquent\Model;

class ScoreType extends Model
{
    /** @var array<int, string> */
    protected $fillable = [
        'name',
        'maximum_score',
    ];

    /** @return array<string, string> */
    protected function casts(): array
    {
        return [
            'name' => ScoreTypeEnum::class,
        ];
    }
}
