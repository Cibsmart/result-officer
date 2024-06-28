<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

final class Faculty extends Model
{
    use SoftDeletes;

    protected $fillable = ['code', 'name'];

    /** @return \Illuminate\Database\Eloquent\Relations\HasMany<\App\Models\Department> */
    public function departments(): HasMany
    {
        return $this->hasMany(Department::class);
    }

}
