<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

final class State extends Model
{
    protected $fillable = ['country_id', 'name'];

}
