<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

final class Enrollment extends Model
{
    /** @var array<int, string> */
    protected $fillable = [
        'student_id',
        'session_id',
        'level_id',
        'year_id',
    ];

    protected $with = ['semesters', 'session', 'year'];

    /** @return \Illuminate\Database\Eloquent\Relations\BelongsTo<\App\Models\Level, \App\Models\Enrollment> */
    public function level(): BelongsTo
    {
        return $this->belongsTo(Level::class);
    }

    /** @return \Illuminate\Database\Eloquent\Relations\HasManyThrough<\App\Models\CourseRegistration> */
    public function courses(): HasManyThrough
    {
        return $this->hasManyThrough(CourseRegistration::class, SemesterEnrollment::class);
    }

    /** @return \Illuminate\Database\Eloquent\Relations\HasMany<\App\Models\SemesterEnrollment> */
    public function semesters(): HasMany
    {
        return $this->hasMany(SemesterEnrollment::class);
    }

    /** @return \Illuminate\Database\Eloquent\Relations\BelongsTo<\App\Models\Session, \App\Models\Enrollment> */
    public function session(): BelongsTo
    {
        return $this->belongsTo(Session::class);
    }

    /** @return \Illuminate\Database\Eloquent\Relations\BelongsTo<\App\Models\Student, \App\Models\Enrollment> */
    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    /** @return \Illuminate\Database\Eloquent\Relations\BelongsTo<\App\Models\Year, \App\Models\Enrollment> */
    public function year(): BelongsTo
    {
        return $this->belongsTo(Year::class);
    }
}
