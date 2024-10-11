<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\YearEnum;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

final class Enrollment extends Model
{
    /** @var array<int, string> */
    protected $fillable = [
        'student_id',
        'session_id',
        'level_id',
        'year',
    ];

    public static function getOrCreate(
        Student $student,
        Session $session,
        Level $level,
    ): self {
        return self::query()->firstOrCreate(
            ['student_id' => $student->id, 'session_id' => $session->id, 'level_id' => $level->id],
            ['year' => YearEnum::FIRST],
        );
    }

    /** @return \Illuminate\Database\Eloquent\Relations\BelongsTo<\App\Models\Level, \App\Models\Enrollment> */
    public function level(): BelongsTo
    {
        return $this->belongsTo(Level::class);
    }

    /** @return \Illuminate\Database\Eloquent\Relations\HasManyThrough<\App\Models\Registration> */
    public function courses(): HasManyThrough
    {
        return $this->hasManyThrough(Registration::class, SemesterEnrollment::class);
    }

    /** @return \Illuminate\Database\Eloquent\Relations\HasMany<\App\Models\SemesterEnrollment> */
    public function semesters(): HasMany
    {
        return $this->hasMany(SemesterEnrollment::class);
    }

    /** @return \Illuminate\Database\Eloquent\Relations\BelongsTo<\App\Models\Session, \App\Models\Enrollment> */
    public function session(): BelongsTo
    {
        return $this->belongsTo(Session::class);
    }

    /** @return \Illuminate\Database\Eloquent\Relations\BelongsTo<\App\Models\Student, \App\Models\Enrollment> */
    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    /** @return array{year: 'App\Enums\YearEnum'} */
    protected function casts(): array
    {
        return [
            'year' => YearEnum::class,
        ];
    }
}
