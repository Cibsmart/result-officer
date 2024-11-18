<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\Year;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

final class SessionEnrollment extends Model
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

    /** @return array{year: 'App\Enums\Year'} */
    protected function casts(): array
    {
        return [
            'year' => Year::class,
        ];
    }
}
