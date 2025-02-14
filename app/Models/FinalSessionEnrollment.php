<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\ComputationStrategy;
use App\Enums\Year;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Support\Facades\Cache;

final class FinalSessionEnrollment extends Model
{
    public static function fromSessionEnrollment(SessionEnrollment $sessionEnrollment, FinalStudent $finalStudent): self
    {
        $finalSessionEnrollment = new self();

        $finalSessionEnrollment->student_id = $sessionEnrollment->student_id;
        $finalSessionEnrollment->final_student_id = $finalStudent->id;
        $finalSessionEnrollment->session_id = $sessionEnrollment->session_id;
        $finalSessionEnrollment->level_id = $sessionEnrollment->level_id;
        $finalSessionEnrollment->year = $sessionEnrollment->year;

        $finalSessionEnrollment->save();

        return $finalSessionEnrollment;
    }

    public static function getOrCreate(
        Student $student,
        FinalStudent $finalStudent,
        Session $session,
        Level $level,
        Year $year = Year::FIRST,
    ): self {
        return
            Cache::remember("final_session_enrollment_{$student->id}_{$session->id}",
                now()->addMinutes(5),
                fn () => self::query()->firstOrCreate(
                    ['student_id' => $student->id, 'session_id' => $session->id],
                    ['final_student_id' => $finalStudent->id, 'level_id' => $level->id, 'year' => $year],
                ));
    }

    /** @return \Illuminate\Database\Eloquent\Relations\BelongsTo<\App\Models\Session, \App\Models\SessionEnrollment> */
    public function session(): BelongsTo
    {
        return $this->belongsTo(Session::class);
    }

    /** @return \Illuminate\Database\Eloquent\Relations\BelongsTo<\App\Models\Student, \App\Models\SessionEnrollment> */
    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    /**
     * phpcs:ignore SlevomatCodingStandard.Files.LineLength
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<\App\Models\FinalSemesterEnrollment, \App\Models\FinalSessionEnrollment>
     */
    public function finalSemesterEnrollments(): HasMany
    {
        return $this->hasMany(FinalSemesterEnrollment::class);
    }

    /**
     * phpcs:ignore SlevomatCodingStandard.Files.LineLength
     * @return \Illuminate\Database\Eloquent\Relations\HasManyThrough<\App\Models\FinalResult, \App\Models\FinalSemesterEnrollment, \App\Models\FinalSessionEnrollment>
     */
    public function finalResults(): HasManyThrough
    {
        return $this->hasManyThrough(FinalResult::class, FinalSemesterEnrollment::class);
    }

    public function getResultCount(): int
    {
        return $this->finalSemesterEnrollments->sum('result_count');
    }

    public function getCreditUnitSum(): int
    {
        return $this->finalSemesterEnrollments->sum('credit_unit_sum');
    }

    public function getGradePointSum(): int
    {
        return $this->finalSemesterEnrollments->sum('grade_point_sum');
    }

    public function getGradePointAverageSum(): float
    {
        return $this->finalSemesterEnrollments->sum('grade_point_average');
    }

    public function getCumulativeGradePointAverage(): float
    {
        $institution = Institution::first();

        if ($institution->strategy === ComputationStrategy::SEMESTER) {
            return round($this->getGradePointAverageSum() / $this->finalSemesterEnrollments->count(), 3);
        }

        return round($this->getGradePointSum() / $this->getCreditUnitSum(), 3);
    }

    public function updateCountSumAndAverages(): void
    {
        $this->credit_unit_sum = $this->getCreditUnitSum();
        $this->grade_point_sum = $this->getGradePointSum();
        $this->grade_point_average_sum = $this->getGradePointAverageSum();
        $this->cumulative_grade_point_average = $this->getCumulativeGradePointAverage();
        $this->result_count = $this->getResultCount();
        $this->save();
    }

    /** @return array{year: 'App\Enums\Year'} */
    protected function casts(): array
    {
        return [
            'year' => Year::class,
        ];
    }

    /** @return \Illuminate\Database\Eloquent\Casts\Attribute<int, float> */
    protected function gradePointAverageSum(): Attribute
    {
        return Attribute::make(
            get: static fn (int $value): float => $value / 1_000,
            set: static fn (float $value): int => (int) ($value * 1_000),
        );
    }

    /** @return \Illuminate\Database\Eloquent\Casts\Attribute<int, float> */
    protected function cumulativeGradePointAverage(): Attribute
    {
        return Attribute::make(
            get: static fn (int $value): float => $value / 1_000,
            set: static fn (float $value): int => (int) ($value * 1_000),
        );
    }
}
