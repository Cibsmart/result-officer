<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

final class Enrollment extends Model
{
    /** @var array<int, string> */
    protected $fillable = [
        'student_id',
        'session_id',
        'level_id',
    ];

    /** @return BelongsTo<\App\Models\Level, \App\Models\Enrollment> */
    public function level(): BelongsTo
    {
        return $this->belongsTo(Level::class);
    }

    /** @return \Illuminate\Database\Eloquent\Relations\HasMany<\App\Models\Result> */
    public function results(): HasMany
    {
        return $this->hasMany(Result::class);
    }

    /** @return BelongsTo<\App\Models\Session, \App\Models\Enrollment> */
    public function session(): BelongsTo
    {
        return $this->belongsTo(Session::class);
    }

    /** @return BelongsTo<\App\Models\Student, \App\Models\Enrollment> */
    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    /** @return BelongsTo<\App\Models\Year, \App\Models\Enrollment> */
    public function year(): BelongsTo
    {
        return $this->belongsTo(Year::class);
    }
}
