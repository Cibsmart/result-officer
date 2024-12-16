<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

final class SemesterEnrollment extends Model
{
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

    public function courseCount(): int
    {
        return $this->registrations->count();
    }

    public function creditUnitSum(): int
    {
        return (int) $this->registrations->sum('credit_unit.value');
    }

    public function gradePointSum(): int
    {
        return (int) $this->registrations->sum('result.grade_point');
    }

    public function gradePointAverage(): float
    {
        return round($this->gradePointSum() / $this->creditUnitSum(), 3);
    }

    public function updateSumsAndAverage(): void
    {
        $this->cus = $this->creditUnitSum();
        $this->gps = $this->gradePointSum();
        $this->gpa = $this->gradePointAverage();
        $this->course_count = $this->courseCount();
        $this->save();
    }

    /** @return \Illuminate\Database\Eloquent\Casts\Attribute<int, float> */
    protected function gpa(): Attribute
    {
        return Attribute::make(
            get: static fn (int $value): float => $value / 1000,
            set: static fn (float $value): int => (int) ($value * 1000),
        );
    }
}
