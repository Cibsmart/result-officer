<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Enrollment extends Model
{
    /** @var array<int, string> */
    protected $fillable = [
        'student_id',
        'session_id',
        'level_id',
    ];
}
