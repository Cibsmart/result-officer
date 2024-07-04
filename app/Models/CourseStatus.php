<?php

namespace App\Models;

use App\Enums\CourseStatusEnum;
use Illuminate\Database\Eloquent\Model;

class CourseStatus extends Model
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
