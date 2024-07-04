<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

final class Session extends Model
{
    protected $table = 'academic_sessions';

    protected $fillable = ['name'];
}
