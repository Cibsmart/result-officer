<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

final class SemesterEnrollment extends Model
{
    protected $fillable = ['enrollment_id', 'semester_id'];

    public static function getOrCreate(Enrollment $sessionEnrollment, Semester $semester): self
    {
        return self::query()->firstOrCreate(
            ['enrollment_id' => $sessionEnrollment->id, 'semester_id' => $semester->id],
        );
    }

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

    /** @return \Illuminate\Database\Eloquent\Relations\HasMany<\App\Models\CourseRegistration> */
    public function courses(): HasMany
    {
        return $this->hasMany(CourseRegistration::class);
    }
}
