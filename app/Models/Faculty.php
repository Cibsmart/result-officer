<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Faculty extends Model
{
    use SoftDeletes;

    protected $fillable = ['code', 'name'];

    /**
     * @return HasMany<Department>
     */
    public function departments(): HasMany
    {
        return $this->hasMany(Department::class);
    }
}
