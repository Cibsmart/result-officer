<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Department extends Model
{
    use softDeletes;

    protected $fillable = ['faculty_id', 'code', 'name'];

    /**
     * @return BelongsTo<Faculty, Department>
     */
    public function faculty(): BelongsTo
    {
        return $this->belongsTo(Faculty::class);
    }

    /**
     * @return HasMany<Program>
     */
    public function programs(): HasMany
    {
        return $this->hasMany(Program::class);
    }
}
