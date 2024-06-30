<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\StudentStatusEnum;
use Illuminate\Database\Eloquent\Model;

final class StudentStatus extends Model
{
    protected $fillable = ['name'];

    /** @return array<string, string> */
    protected function casts(): array
    {
        return [
            'name' => StudentStatusEnum::class,
        ];
    }
}
