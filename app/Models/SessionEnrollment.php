<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\CumulativeComputationStrategy;
use App\Enums\Year;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

final class SessionEnrollment extends Model
{
    public static function getOrCreate(
        Student $student,
        Session $session,
        Level $level,
    ): self {
        $sessionEnrollment = self::query()->firstOrCreate(
            ['student_id' => $student->id, 'session_id' => $session->id],
            ['level_id' => $level->id, 'year' => Year::FIRST],
        );

        $student->updateStatus($student->getStatus());

        return $sessionEnrollment;
    }

    public function updateYear(Year $year): void
    {
        $this->year = $year;
        $this->save();
    }

    /** @return \Illuminate\Database\Eloquent\Relations\BelongsTo<\App\Models\Level, \App\Models\SessionEnrollment> */
    public function level(): BelongsTo
    {
        return $this->belongsTo(Level::class);
    }

    /**
     * phpcs:ignore SlevomatCodingStandard.Files.LineLength
     * @return \Illuminate\Database\Eloquent\Relations\HasManyThrough<\App\Models\Registration, \App\Models\SemesterEnrollment, \App\Models\SessionEnrollment>
     */
    public function registrations(): HasManyThrough
    {
        return $this->hasManyThrough(Registration::class, SemesterEnrollment::class);
    }

    /**
     * phpcs:ignore SlevomatCodingStandard.Files.LineLength
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<\App\Models\SemesterEnrollment, \App\Models\SessionEnrollment>
     */
    public function semesterEnrollments(): HasMany
    {
        return $this->hasMany(SemesterEnrollment::class);
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

    public function courseCount(): int
    {
        return $this->semesterEnrollments->sum('course_count');
    }

    public function creditUnitSum(): int
    {
        return $this->semesterEnrollments->sum('cus');
    }

    public function gradePointSum(): int
    {
        return $this->semesterEnrollments->sum('gps');
    }

    public function gradePointAverageSum(): float
    {
        return $this->semesterEnrollments->sum('gpa');
    }

    public function cumulativeGradePointAverage(): float
    {
        $institution = Institution::first();

        if ($institution->strategy === CumulativeComputationStrategy::SEMESTER) {
            return round($this->gradePointAverageSum() / $this->semesterEnrollments->count(), 3);
        }

        return round($this->gradePointSum() / $this->creditUnitSum(), 3);
    }

    public function updateCountSumAndAverages(): void
    {
        $this->cus = $this->creditUnitSum();
        $this->gps = $this->gradePointSum();
        $this->gpas = $this->gradePointAverageSum();
        $this->cgpa = $this->cumulativeGradePointAverage();
        $this->course_count = $this->courseCount();
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
    protected function gpas(): Attribute
    {
        return Attribute::make(
            get: static fn (int $value): float => $value / 1000,
            set: static fn (float $value): int => (int) ($value * 1000),
        );
    }

    /** @return \Illuminate\Database\Eloquent\Casts\Attribute<int, float> */
    protected function cgpa(): Attribute
    {
        return Attribute::make(
            get: static fn (int $value): float => $value / 1000,
            set: static fn (float $value): int => (int) ($value * 1000),
        );
    }
}
