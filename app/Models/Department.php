<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

final class Department extends Model
{

    use softDeletes;

    protected $fillable = ['faculty_id', 'code', 'name'];

    /** @return \Illuminate\Database\Eloquent\Relations\BelongsTo<\App\Models\Faculty, \App\Models\Department> */
    public function faculty(): BelongsTo
    {
        return $this->belongsTo(Faculty::class);
    }

    /** @return \Illuminate\Database\Eloquent\Relations\HasMany<\App\Models\Program> */
    public function programs(): HasMany
    {
        return $this->hasMany(Program::class);
    }

}
