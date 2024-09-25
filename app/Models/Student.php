<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\Gender;
use App\Enums\RecordSource;
use App\Enums\StudentStatusEnum;
use App\Values\DateValue;
use App\Values\RegistrationNumber;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

final class Student extends Model
{
    /** @var array<int, string> */
    protected $fillable = [
        'registration_number',
        'last_name',
        'first_name',
        'other_names',
        'gender',
        'date_of_birth',
        'state_id',
        'program_id',
        'entry_session_id',
        'entry_level_id',
        'entry_mode_id',
        'jamb_registration_number',
        'online_id',
        'email',
        'local_government',
        'phone_number',
        'source',
        'status',
    ];

    /** @throws \Exception */
    public static function createFromRawStudent(RawStudent $rawStudent): void
    {
        $registrationNumber = RegistrationNumber::new($rawStudent->registration_number);
        $department = Department::getUsingOnlineId($rawStudent->department_id);
        $dateOfBirth = DateValue::fromString($rawStudent->date_of_birth);

        $student = new self();
        $student->date_of_birth = $dateOfBirth->value;
        $student->email = $rawStudent->email;
        $student->entry_level_id = Level::getUsingName($rawStudent->entry_level)->id;
        $student->entry_mode_id = EntryMode::getUsingCode($rawStudent->entry_mode)->id;
        $student->entry_session_id = Session::getUsingName($rawStudent->entry_session)->id;
        $student->first_name = $rawStudent->first_name;
        $student->gender = Gender::from($rawStudent->gender);
        $student->jamb_registration_number = $rawStudent->jamb_registration_number;
        $student->last_name = $rawStudent->last_name;
        $student->local_government = $rawStudent->local_government;
        $student->online_id = $rawStudent->online_id;
        $student->other_names = $rawStudent->other_names;
        $student->phone_number = $rawStudent->phone_number;
        $student->program_id = Program::getFromDepartmentAndName($department, $rawStudent->option)->id;
        $student->registration_number = $registrationNumber->value;
        $student->source = RecordSource::PORTAL;
        $student->state_id = State::getUsingName($rawStudent->state)->id;
        $student->status = StudentStatusEnum::NEW;

        $student->save();
    }

    /** @return \Illuminate\Database\Eloquent\Relations\HasManyThrough<\App\Models\CourseRegistration> */
    public function courses(): HasManyThrough
    {
        return $this->through('enrollments')->has('courses');
    }

    /** @return \Illuminate\Database\Eloquent\Relations\BelongsTo<\App\Models\State, \App\Models\Student> */
    public function state(): BelongsTo
    {
        return $this->belongsTo(State::class);
    }

    /** @return \Illuminate\Database\Eloquent\Relations\HasMany<\App\Models\Enrollment> */
    public function enrollments(): HasMany
    {
        return $this->hasMany(Enrollment::class);
    }

    /** @return \Illuminate\Database\Eloquent\Relations\BelongsTo<\App\Models\Level, \App\Models\Student> */
    public function entryLevel(): BelongsTo
    {
        return $this->belongsTo(Level::class);
    }

    /** @return \Illuminate\Database\Eloquent\Relations\BelongsTo<\App\Models\EntryMode, \App\Models\Student> */
    public function entryMode(): BelongsTo
    {
        return $this->belongsTo(EntryMode::class);
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

    /** @return \Illuminate\Database\Eloquent\Relations\HasManyThrough<\App\Models\SemesterEnrollment> */
    public function semesters(): HasManyThrough
    {
        return $this->hasManyThrough(SemesterEnrollment::class, Enrollment::class);
    }

    public function allowEGrade(): bool
    {
        return RegistrationNumber::new($this->registration_number)->allowEGrade();
    }

    /**
     * @return array{date_of_birth: 'date', gender: 'App\Enums\Gender', source: 'App\Enums\RecordSource',
     *     status: 'App\Enums\StudentStatusEnum'}
     */
    protected function casts(): array
    {
        return [
            'date_of_birth' => 'date',
            'gender' => Gender::class,
            'source' => RecordSource::class,
            'status' => StudentStatusEnum::class,
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
            ): string => "{$attributes['last_name']} {$attributes['first_name']} {$attributes['other_names']}",
        );
    }

    /** @return \Illuminate\Database\Eloquent\Casts\Attribute<string, string> */
    protected function lastName(): Attribute
    {
        return Attribute::make(
            set: static fn (string $value): string => strtoupper($value),
        );
    }

    /** @return \Illuminate\Database\Eloquent\Casts\Attribute<string, string> */
    protected function firstName(): Attribute
    {
        return Attribute::make(
            set: static fn (string $value): string => strtoupper($value),
        );
    }

    /** @return \Illuminate\Database\Eloquent\Casts\Attribute<string, string> */
    protected function otherNames(): Attribute
    {
        return Attribute::make(
            set: static fn (string $value): string => strtoupper($value),
        );
    }

    /** @return \Illuminate\Database\Eloquent\Casts\Attribute<string, string> */
    protected function email(): Attribute
    {
        return Attribute::make(
            set: static fn (string $value): string => strtolower($value),
        );
    }

    /** @return \Illuminate\Database\Eloquent\Casts\Attribute<string, string> */
    protected function localGovernment(): Attribute
    {
        return Attribute::make(
            set: static fn (string $value): string => strtoupper($value),
        );
    }
}
