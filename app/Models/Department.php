<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\SoftDeletes;

final class Department extends Model
{
    use softDeletes;

    protected $fillable = ['faculty_id', 'code', 'name', 'online_id'];

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

    /** @return \Illuminate\Database\Eloquent\Relations\HasManyThrough<\App\Models\Student> */
    public function students(): HasManyThrough
    {
        return $this->hasManyThrough(Student::class, Program::class);
    }

    /** @return array<string, string> */
    protected function casts(): array
    {
        return [
            'is_active' => 'bool',
        ];
    }
}
