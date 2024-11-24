<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

final class SemesterEnrollment extends Model
{
    protected $fillable = ['session_enrollment_id', 'semester_id'];

    public static function getOrCreate(SessionEnrollment $sessionEnrollment, Semester $semester): self
    {
        return self::query()->firstOrCreate(
            ['session_enrollment_id' => $sessionEnrollment->id, 'semester_id' => $semester->id],
        );
    }

    /**
     * phpcs:ignore SlevomatCodingStandard.Files.LineLength
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany<\App\Models\VettingReport, \App\Models\SemesterEnrollment>
     */
    public function vettingReports(): MorphMany
    {
        return $this->MorphMany(VettingReport::class, 'vettable');
    }

    /**
     * phpcs:ignore SlevomatCodingStandard.Files.LineLength
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<\App\Models\SessionEnrollment, \App\Models\SemesterEnrollment>
     */
    public function sessionEnrollment(): BelongsTo
    {
        return $this->belongsTo(SessionEnrollment::class);
    }

    /** @return \Illuminate\Database\Eloquent\Relations\BelongsTo<\App\Models\Semester, \App\Models\SemesterEnrollment> */
    public function semester(): BelongsTo
    {
        return $this->belongsTo(Semester::class);
    }

    /** @return \Illuminate\Database\Eloquent\Relations\HasMany<\App\Models\Registration, \App\Models\SemesterEnrollment> */
    public function registrations(): HasMany
    {
        return $this->hasMany(Registration::class);
    }
}
