<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Facades\Cache;

final class SemesterEnrollment extends Model
{
    public static function getOrCreate(SessionEnrollment $sessionEnrollment, Semester $semester): self
    {
        return
            Cache::remember("semester_enrollment_{$sessionEnrollment->id}_{$semester->id}",
                now()->addMinutes(5),
                fn () => self::query()->firstOrCreate(
                    ['session_enrollment_id' => $sessionEnrollment->id, 'semester_id' => $semester->id],
                ));
    }

    /**
     * @phpstan-return \Illuminate\Database\Eloquent\Relations\MorphMany<\App\Models\VettingReport, $this>
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany<\App\Models\VettingReport, static>
     */
    public function vettingReports(): MorphMany
    {
        return $this->MorphMany(VettingReport::class, 'vettable');
    }

    /**
     * @phpstan-return \Illuminate\Database\Eloquent\Relations\BelongsTo<\App\Models\SessionEnrollment, $this>
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<\App\Models\SessionEnrollment, static>
     */
    public function sessionEnrollment(): BelongsTo
    {
        return $this->belongsTo(SessionEnrollment::class);
    }

    /**
     * @phpstan-return \Illuminate\Database\Eloquent\Relations\BelongsTo<\App\Models\Semester, $this>
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<\App\Models\Semester, static>
     */
    public function semester(): BelongsTo
    {
        return $this->belongsTo(Semester::class);
    }

    /**
     * @phpstan-return \Illuminate\Database\Eloquent\Relations\HasMany<\App\Models\Registration, $this>
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<\App\Models\Registration, static>
     */
    public function registrations(): HasMany
    {
        return $this->hasMany(Registration::class);
    }

    /**
     * @phpstan-return \Illuminate\Database\Eloquent\Relations\BelongsTo<\App\Models\ProgramCurriculumSemester, $this>
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<\App\Models\ProgramCurriculumSemester, static>
     */
    public function programCurriculumSemester(): BelongsTo
    {
        return $this->belongsTo(ProgramCurriculumSemester::class);
    }
}
