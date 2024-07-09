<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class CourseRegistration extends Model
{
    /** @var array<int, string> */
    protected $fillable = [
        'enrollment_id',
        'course_id',
        'credit_unit',
        'course_status_id',
    ];

    /** @return BelongsTo<\App\Models\SemesterEnrollment, \App\Models\CourseRegistration> */
    public function semester(): BelongsTo
    {
        return $this->belongsTo(SemesterEnrollment::class);
    }

    /** @return BelongsTo<\App\Models\Course, \App\Models\CourseRegistration> */
    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    /** @return \Illuminate\Database\Eloquent\Relations\BelongsTo<\App\Models\CourseStatus, \App\Models\CourseRegistration> */
    public function courseStatus(): BelongsTo
    {
        return $this->belongsTo(CourseStatus::class);
    }

    /** @return HasOne<\App\Models\Result> */
    public function result(): HasOne
    {
        return $this->hasOne(Result::class);
    }
}
