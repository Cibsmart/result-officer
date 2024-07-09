<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SemesterEnrollment extends Model
{
    protected $fillable = ['enrollment_id', 'semester_id'];

    /** @return \Illuminate\Database\Eloquent\Relations\BelongsTo<\App\Models\Enrollment, \App\Models\SemesterEnrollment> */
    public function enrollment(): BelongsTo
    {
        return $this->belongsTo(Enrollment::class);
    }

    /** @return \Illuminate\Database\Eloquent\Relations\BelongsTo<\App\Models\Semester, \App\Models\SemesterEnrollment> */
    public function semester(): BelongsTo
    {
        return $this->belongsTo(Semester::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<\App\Models\CourseRegistration>
     */
    public function courses(): HasMany
    {
        return $this->hasMany(CourseRegistration::class);
    }
}
