<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\GenderEnum;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

final class Student extends Model
{
    /** @var array<int, string> */
    protected $fillable = [
        'matriculation_number',
        'last_name',
        'first_name',
        'other_names',
        'gender',
        'date_of_birth',
        'country_id',
        'program_id',
        'entry_session_id',
        'entry_level_id',
        'entry_mode_id',
        'jamb_registration_number',
        'online_id',
    ];

    /** @return \Illuminate\Database\Eloquent\Relations\HasManyThrough<\App\Models\CourseRegistration> */
    public function courses(): HasManyThrough
    {
        return $this->through('enrollments')->has('courses');
    }

    /** @return \Illuminate\Database\Eloquent\Relations\BelongsTo<\App\Models\Country, \App\Models\Student> */
    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
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

    /** @return array<string, string> */
    protected function casts(): array
    {
        return [
            'date_of_birth' => 'date',
            'gender' => GenderEnum::class,
        ];
    }
}
