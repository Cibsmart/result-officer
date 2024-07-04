<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\ScoreTypeEnum;
use Illuminate\Database\Eloquent\Model;

final class ScoreType extends Model
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
