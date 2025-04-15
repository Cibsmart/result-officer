<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Cache;

final class FinalSemesterEnrollment extends Model
{
    public static function fromSemesterEnrollment(
        SemesterEnrollment $semesterEnrollment,
        FinalSessionEnrollment $finalSessionEnrollment,
    ): self {

        $finalSemesterEnrollment = new self();

        $finalSemesterEnrollment->final_session_enrollment_id = $finalSessionEnrollment->id;
        $finalSemesterEnrollment->semester_id = $semesterEnrollment->semester_id;

        $finalSemesterEnrollment->save();

        return $finalSemesterEnrollment;
    }

    public static function getOrCreate(FinalSessionEnrollment $sessionEnrollment, Semester $semester): self
    {
        return
            Cache::remember("final_semester_enrollment_{$sessionEnrollment->id}_{$semester->id}",
                now()->addMinutes(5),
                fn () => self::query()->firstOrCreate(
                    ['final_session_enrollment_id' => $sessionEnrollment->id, 'semester_id' => $semester->id],
                ));
    }

    /**
     * @phpstan-return \Illuminate\Database\Eloquent\Relations\HasMany<\App\Models\FinalResult, $this>
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<\App\Models\FinalResult, static>
     */
    public function finalResults(): HasMany
    {
        return $this->hasMany(FinalResult::class);
    }

    /**
     * @phpstan-return \Illuminate\Database\Eloquent\Relations\BelongsTo<\App\Models\FinalSessionEnrollment, $this>
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<\App\Models\FinalSessionEnrollment, static>
     */
    public function finalSessionEnrollment(): BelongsTo
    {
        return $this->belongsTo(FinalSessionEnrollment::class);
    }

    /**
     * @phpstan-return \Illuminate\Database\Eloquent\Relations\BelongsTo<\App\Models\Semester, $this>
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<\App\Models\Semester, static>
     */
    public function semester(): BelongsTo
    {
        return $this->belongsTo(Semester::class);
    }

    public function session(): Session
    {
        $session = $this->finalSessionEnrollment->session;
        assert($session instanceof Session);

        return $session;
    }

    public function getResultCount(): int
    {
        return $this->finalResults->count();
    }

    public function getCreditUnitSum(): int
    {
        return (int) $this->finalResults->sum('credit_unit.value');
    }

    public function getGradePointSum(): int
    {
        return (int) $this->finalResults->sum('grade_point');
    }

    public function getGradePointAverage(): float
    {
        $creditUnitSum = $this->getCreditUnitSum();

        $average = $creditUnitSum === 0
            ? 0
            : $this->getGradePointSum() / $creditUnitSum;

        return round($average, 3);
    }

    public function updateSumsAndAverage(): void
    {
        $this->credit_unit_sum = $this->getCreditUnitSum();
        $this->grade_point_sum = $this->getGradePointSum();
        $this->grade_point_average = $this->getGradePointAverage();
        $this->result_count = $this->getResultCount();
        $this->save();
    }

    /** @return \Illuminate\Database\Eloquent\Casts\Attribute<int, float> */
    protected function gradePointAverage(): Attribute
    {
        return Attribute::make(
            get: static fn (int $value): float => $value / 1_000,
            set: static fn (float $value): int => (int) ($value * 1_000),
        );
    }
}
