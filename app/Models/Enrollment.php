<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

final class Enrollment extends Model
{
    /** @var array<int, string> */
    protected $fillable = [
        'student_id',
        'session_id',
        'level_id',
    ];
}
