<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\CourseStatusEnum;
use Illuminate\Database\Eloquent\Model;

final class CourseStatus extends Model
{
    protected $fillable = ['code', 'name'];

    /** @return array<string, string> */
    protected function casts(): array
    {
        return [
            'code' => CourseStatusEnum::class,
        ];
    }
}
