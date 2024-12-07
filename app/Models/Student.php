<?php

declare(strict_types=1);

namespace App\Models;

use App\Data\Models\StudentModelData;
use App\Enums\CumulativeComputationStrategy;
use App\Enums\EntryMode;
use App\Enums\Gender;
use App\Enums\ProgramDuration;
use App\Enums\RecordSource;
use App\Enums\StudentStatus;
use App\Enums\VettingEventStatus;
use App\Values\RegistrationNumber;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;

final class Student extends Model
{
    use SoftDeletes;

    public static function createFromRawStudent(RawStudent $rawStudent): self
    {
        $student = StudentModelData::fromRawStudent($rawStudent)->getModel();

        $student->save();

        return $student;
    }

    public static function getUsingRegistrationNumber(string $registrationNumber): self
    {
        return self::query()->where('registration_number', $registrationNumber)->firstOrFail();
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    /**
     * phpcs:ignore SlevomatCodingStandard.Files.LineLength
     * @return array{date_of_birth: 'date', entry_mode: 'App\Enums\EntryMode', gender: 'App\Enums\Gender', source: 'App\Enums\RecordSource', status: 'App\Enums\StudentStatus'}
     */
    protected function casts(): array
    {
        return [
            'date_of_birth' => 'date',
            'entry_mode' => EntryMode::class,
            'gender' => Gender::class,
            'source' => RecordSource::class,
            'status' => StudentStatus::class,
        ];
    }

    /** @return \Illuminate\Database\Eloquent\Relations\MorphMany<\App\Models\VettingReport, \App\Models\Student> */
    public function vettingReports(): MorphMany
    {
        return $this->MorphMany(VettingReport::class, 'vettable');
    }

    /** @return \Illuminate\Database\Eloquent\Relations\HasOne<\App\Models\VettingEvent, \App\Models\Student> */
    public function vettingEvent(): HasOne
    {
        return $this->hasOne(VettingEvent::class);
    }

    /**
     * phpcs:ignore SlevomatCodingStandard.Files.LineLength
     * @return \Illuminate\Database\Eloquent\Relations\HasManyThrough<\App\Models\Registration, \Illuminate\Database\Eloquent\Model, \App\Models\Student>
     */
    public function registrations(): HasManyThrough
    {
        return $this->through('semesterEnrollments')->has('registrations');
    }

    /** @return \Illuminate\Database\Eloquent\Relations\BelongsTo<\App\Models\LocalGovernment, \App\Models\Student> */
    public function government(): BelongsTo
    {
        return $this->belongsTo(LocalGovernment::class, 'local_government_id');
    }

    /** @return \Illuminate\Database\Eloquent\Relations\HasMany<\App\Models\SessionEnrollment, \App\Models\Student> */
    public function sessionEnrollments(): HasMany
    {
        return $this->hasMany(SessionEnrollment::class);
    }

    /** @return \Illuminate\Database\Eloquent\Relations\BelongsTo<\App\Models\Level, \App\Models\Student> */
    public function entryLevel(): BelongsTo
    {
        return $this->belongsTo(Level::class);
    }

    /** @return \Illuminate\Database\Eloquent\Relations\BelongsTo<\App\Models\Session, \App\Models\Student> */
    public function entrySession(): BelongsTo
    {
        return $this->belongsTo(Session::class);
    }

    /** @return \Illuminate\Database\Eloquent\Relations\BelongsTo<\App\Models\Program, \App\Models\Student> */
    public function program(): BelongsTo
    {
        return $this->belongsTo(Program::class);
    }

    public function department(): Department
    {
        $department = $this->program->department;
        assert($department instanceof Department);

        return $department;
    }

    /**
     * phpcs:ignore SlevomatCodingStandard.Files.LineLength
     * @return \Illuminate\Database\Eloquent\Relations\HasManyThrough<\App\Models\SemesterEnrollment, \App\Models\SessionEnrollment, \App\Models\Student>
     */
    public function semesterEnrollments(): HasManyThrough
    {
        return $this->hasManyThrough(SemesterEnrollment::class, SessionEnrollment::class);
    }

    /** @return \Illuminate\Database\Eloquent\Relations\HasMany<\App\Models\StatusChangeEvent, \App\Models\Student> */
    public function statusChangeEvents(): HasMany
    {
        return $this->hasMany(StatusChangeEvent::class);
    }

    public function programCurriculum(): ?ProgramCurriculum
    {
        return ProgramCurriculum::query()
            ->where('program_id', $this->program->id)
            ->where('entry_session_id', $this->entrySession->id)
            ->where('entry_mode', $this->entry_mode)
            ->first();
    }

    public function allowEGrade(): bool
    {
        return RegistrationNumber::new($this->registration_number)->allowEGrade();
    }

    public function updateStatus(StudentStatus $status): void
    {
        if ($this->status === StudentStatus::GRADUATED || $this->status === $status) {
            return;
        }

        $this->status = $status->value;
        $this->save();
    }

    public function getStatus(): StudentStatus
    {
        if ($this->inExtraYear()) {
            return StudentStatus::EXTRA_YEAR;
        }

        if ($this->inFinalYear()) {
            return StudentStatus::FINAL_YEAR;
        }

        return StudentStatus::ACTIVE;
    }

    public function canBeCleared(): bool
    {
        return $this->vettingEvent !== null
            && StudentStatus::canBeCleared($this->status)
            && VettingEventStatus::passed($this->vettingEvent->status);
    }

    public function courseCount(): int
    {
        return $this->sessionEnrollments->sum('course_count');
    }

    public function creditUnitSum(): int
    {
        return $this->sessionEnrollments->sum('cus');
    }

    public function gradePointSum(): int
    {
        return $this->sessionEnrollments->sum('gps');
    }

    public function cumulativeGradePointAverageSum(): float
    {
        return $this->sessionEnrollments->sum('cgpa');
    }

    public function finalCumulativeGradePointAverage(): float
    {
        if ($this->sessionEnrollments->count() === 0 && $this->creditUnitSum() === 0) {
            return 0.000;
        }

        if (Institution::first()->strategy === CumulativeComputationStrategy::SEMESTER) {
            return round($this->cumulativeGradePointAverageSum() / $this->sessionEnrollments->count(), 3);
        }

        return round($this->gradePointSum() / $this->creditUnitSum(), 3);
    }

    public function updateCountSumAndAverages(): void
    {
        $this->cus = $this->creditUnitSum();
        $this->gps = $this->gradePointSum();
        $this->cgpas = $this->cumulativeGradePointAverageSum();
        $this->fcgpa = $this->finalCumulativeGradePointAverage();
        $this->course_count = $this->courseCount();
        $this->save();
    }

    /** @return \Illuminate\Database\Eloquent\Casts\Attribute<string, string> */
    protected function name(): Attribute
    {
        return Attribute::make(
            /** @phpcsSuppress SlevomatCodingStandard.Functions.UnusedParameter.UnusedParameter */
            get: fn (
                ?string $value,
                array $attributes,
            ): string => "{$attributes['last_name']} {$attributes['first_name']} {$attributes['other_names']}",
        );
    }

    /** @return \Illuminate\Database\Eloquent\Casts\Attribute<string, string> */
    protected function lastName(): Attribute
    {
        return Attribute::make(set: static fn (string $value): string => strtoupper($value));
    }

    /** @return \Illuminate\Database\Eloquent\Casts\Attribute<string, string> */
    protected function firstName(): Attribute
    {
        return Attribute::make(set: static fn (string $value): string => strtoupper($value));
    }

    /** @return \Illuminate\Database\Eloquent\Casts\Attribute<string, string> */
    protected function otherNames(): Attribute
    {
        return Attribute::make(
            set: static fn (?string $value): ?string => is_null($value) ? null : strtoupper($value),
        );
    }

    /** @return \Illuminate\Database\Eloquent\Casts\Attribute<string, string> */
    protected function email(): Attribute
    {
        return Attribute::make(set: static fn (string $value): string => strtolower($value));
    }

    /** @return \Illuminate\Database\Eloquent\Casts\Attribute<string, string> */
    protected function localGovernment(): Attribute
    {
        return Attribute::make(
            set: static fn (string $value): string => strtoupper($value),
        );
    }

    /** @return \Illuminate\Database\Eloquent\Casts\Attribute<int, float> */
    protected function cgpas(): Attribute
    {
        return Attribute::make(
            get: static fn (int $value): float => $value / 1000,
            set: static fn (float $value): int => (int) ($value * 1000),
        );
    }

    /** @return \Illuminate\Database\Eloquent\Casts\Attribute<int, float> */
    protected function fcgpa(): Attribute
    {
        return Attribute::make(
            get: static fn (int $value): float => $value / 1000,
            set: static fn (float $value): int => (int) ($value * 1000),
        );
    }

    private function inFinalYear(): bool
    {
        if ($this->status === StudentStatus::FINAL_YEAR) {
            return true;
        }

        $duration = $this->program->duration;
        assert($duration instanceof ProgramDuration);

        $takenFinalYearCourse = $this->sessionEnrollments()->where('level_id', $duration->level())->exists();

        return $takenFinalYearCourse || $this->sessionEnrollments()->count() === $duration->value;
    }

    private function inExtraYear(): bool
    {
        if ($this->status === StudentStatus::EXTRA_YEAR) {
            return true;
        }

        $duration = $this->program->duration;
        assert($duration instanceof ProgramDuration);

        return $this->sessionEnrollments()->count() > $duration->value;
    }
}
