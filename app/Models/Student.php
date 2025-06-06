<?php

declare(strict_types=1);

namespace App\Models;

use App\Data\Models\StudentModelData;
use App\Enums\EntryMode;
use App\Enums\Gender;
use App\Enums\ProgramDuration;
use App\Enums\RecordSource;
use App\Enums\StudentField;
use App\Enums\StudentRelatedField;
use App\Enums\StudentStatus;
use App\Enums\VettingEventStatus;
use App\Values\DateValue;
use App\Values\RegistrationNumber;
use Exception;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Cache;

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
        return
            Cache::remember("student_{$registrationNumber}",
                now()->addMinutes(5),
                fn () => self::query()->where('registration_number', $registrationNumber)->firstOrFail());
    }

    /** @throws \Exception */
    public static function deleteRegistration(self $student): void
    {
        if (in_array($student->status, StudentStatus::archivedStates(), true)) {
            throw new Exception("Cannot delete {$student->status->value} student");
        }

        $student->delete();
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
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
     * @phpstan-return \Illuminate\Database\Eloquent\Relations\HasOne<\App\Models\VettingEvent, $this>
     * @return \Illuminate\Database\Eloquent\Relations\HasOne<\App\Models\VettingEvent, static>
     */
    public function vettingEvent(): HasOne
    {
        return $this->hasOne(VettingEvent::class);
    }

    /**
     * @phpstan-return \Illuminate\Database\Eloquent\Relations\BelongsTo<\App\Models\LocalGovernment, $this>
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<\App\Models\LocalGovernment, static>
     */
    public function lga(): BelongsTo
    {
        return $this->belongsTo(LocalGovernment::class, 'local_government_id');
    }

    /**
     * @phpstan-return \Illuminate\Database\Eloquent\Relations\HasMany<\App\Models\SessionEnrollment, $this>
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<\App\Models\SessionEnrollment, static>
     */
    public function sessionEnrollments(): HasMany
    {
        return $this->hasMany(SessionEnrollment::class);
    }

    /**
     * @phpstan-return \Illuminate\Database\Eloquent\Relations\BelongsTo<\App\Models\Level, $this>
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<\App\Models\Level, static>
     */
    public function entryLevel(): BelongsTo
    {
        return $this->belongsTo(Level::class);
    }

    /**
     * @phpstan-return \Illuminate\Database\Eloquent\Relations\BelongsTo<\App\Models\Session, $this>
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<\App\Models\Session, static>
     */
    public function entrySession(): BelongsTo
    {
        return $this->belongsTo(Session::class);
    }

    /**
     * @phpstan-return \Illuminate\Database\Eloquent\Relations\BelongsTo<\App\Models\Program, $this>
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<\App\Models\Program, static>
     */
    public function program(): BelongsTo
    {
        return $this->belongsTo(Program::class);
    }

    /**
     * @phpstan-return \Illuminate\Database\Eloquent\Relations\HasOne<\App\Models\FinalStudent, $this>
     * @return \Illuminate\Database\Eloquent\Relations\HasOne<\App\Models\FinalStudent, static>
     */
    public function FinalStudent(): HasOne
    {
        return $this->hasOne(FinalStudent::class);
    }

    public function department(): Department
    {
        $department = $this->program->department;
        assert($department instanceof Department);

        return $department;
    }

    /**
     * phpcs:ignore SlevomatCodingStandard.Files.LineLength
     * @phpstan-return \Illuminate\Database\Eloquent\Relations\HasManyThrough<\App\Models\SemesterEnrollment, \App\Models\SessionEnrollment, $this>
     * phpcs:ignore SlevomatCodingStandard.Files.LineLength
     * @return \Illuminate\Database\Eloquent\Relations\HasManyThrough<\App\Models\SemesterEnrollment, \App\Models\SessionEnrollment, static>
     */
    public function semesterEnrollments(): HasManyThrough
    {
        return $this->hasManyThrough(SemesterEnrollment::class, SessionEnrollment::class);
    }

    /**
     * phpcs:ignore SlevomatCodingStandard.Files.LineLength
     * @phpstan-return \Illuminate\Database\Eloquent\Relations\HasManyThrough<\App\Models\FinalSemesterEnrollment, \App\Models\FinalSessionEnrollment, $this>
     * phpcs:ignore SlevomatCodingStandard.Files.LineLength
     * @return \Illuminate\Database\Eloquent\Relations\HasManyThrough<\App\Models\FinalSemesterEnrollment, \App\Models\FinalSessionEnrollment, static>
     */
    public function finalSemesterEnrollments(): HasManyThrough
    {
        return $this->hasManyThrough(FinalSemesterEnrollment::class, FinalSessionEnrollment::class);
    }

    /**
     * @phpstan-return \Illuminate\Database\Eloquent\Relations\HasMany<\App\Models\StatusChangeEvent, $this>
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<\App\Models\StatusChangeEvent, static>
     */
    public function statusChangeEvents(): HasMany
    {
        return $this->hasMany(StatusChangeEvent::class);
    }

    public function updateRegistrationNumber(RegistrationNumber $registrationNumber): void
    {
        $this->registration_number = $registrationNumber->value;
        $this->number = $registrationNumber->number();
        $this->slug = $registrationNumber->slug();
        $this->save();
    }

    /** @param array{last_name?: string, first_name?: string, other_names?: string} $names */
    public function updateNames(array $names): void
    {
        $this->update($names);
    }

    public function programCurriculum(): ?ProgramCurriculum
    {
        return ProgramCurriculum::query()
            ->where('program_id', $this->program_id)
            ->where('entry_session_id', $this->entry_session_id)
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

        $this->status = $status;
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

    public function updateField(StudentField $field, string $newValue): void
    {
        $this->{$field->value} = $newValue;
        $this->save();
    }

    public function updateRelatedField(StudentRelatedField $field, int $newValue): void
    {
        $this->{$field->value} = $newValue;
        $this->save();
    }

    public function updateGender(Gender $gender): void
    {
        $this->gender = $gender;
        $this->save();
    }

    public function updateEntryMode(EntryMode $entryMode): void
    {
        $this->entry_mode = $entryMode;
        $this->save();
    }

    public function updateBirthDate(DateValue $birthDate): void
    {
        $this->date_of_birth = $birthDate->value;
        $this->save();
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

    /** @return \Illuminate\Database\Eloquent\Casts\Attribute<string, string> */
    protected function name(): Attribute
    {
        return Attribute::make(
            /** @phpcsSuppress SlevomatCodingStandard.Functions.UnusedParameter.UnusedParameter */
            get: fn (
                ?string $value,
                array $attributes,
            ): string => mb_trim("{$attributes['last_name']} {$attributes['first_name']} {$attributes['other_names']}"),
        );
    }

    /** @return \Illuminate\Database\Eloquent\Casts\Attribute<string, string> */
    protected function lastName(): Attribute
    {
        return Attribute::make(set: static fn (string $value): string => mb_strtoupper($value));
    }

    /** @return \Illuminate\Database\Eloquent\Casts\Attribute<string, string> */
    protected function firstName(): Attribute
    {
        return Attribute::make(set: static fn (string $value): string => mb_strtoupper($value));
    }

    /** @return \Illuminate\Database\Eloquent\Casts\Attribute<string, string> */
    protected function otherNames(): Attribute
    {
        return Attribute::make(
            set: static fn (?string $value): ?string => is_null($value) ? null : mb_strtoupper($value),
        );
    }

    /** @return \Illuminate\Database\Eloquent\Casts\Attribute<string, string> */
    protected function email(): Attribute
    {
        return Attribute::make(set: static fn (string $value): string => mb_strtolower($value));
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
