<?php

declare(strict_types=1);

namespace App\Models;

use App\Values\SessionValue;
use Illuminate\Database\Eloquent\Model;

final class Session extends Model
{
    protected $table = 'academic_sessions';

    protected $fillable = ['name'];

    public function firstYear(): int
    {
        return SessionValue::new($this->name)->lastYear();
    }

    public function lastYear(): int
    {
        return SessionValue::new($this->name)->lastYear();
    }
}
