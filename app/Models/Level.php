<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\LevelEnum;
use Illuminate\Database\Eloquent\Model;

final class Level extends Model
{
    /** @var array<int, string> */
    protected $fillable = ['name'];

    /** @return array<string, string> */
    protected function casts(): array
    {
        return [
            'name' => LevelEnum::class,
        ];
    }
}
