<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\CourseStatus;
use App\Enums\CreditUnit;
use App\Enums\RecordSource;
use App\Values\DateValue;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;

final class Registration extends Model
{
    use SoftDeletes;

    /** @var array<int, string> */
    protected $fillable = [
        'semester_enrollment_id',
        'course_id',
        'credit_unit',
        'course_status',
    ];

    public static function createFromRawRegistration(
        RawRegistration $rawRegistration,
        SemesterEnrollment $semesterEnrollment,
        Course $course,
    ): self {
        $registrationDate = DateValue::fromString($rawRegistration->registration_date);
        $registration = new self();

        $registration->course_id = $course->id;
        $registration->credit_unit = CreditUnit::from((int) $rawRegistration->credit_unit)->value;
        $registration->course_status = CourseStatus::FRESH;
        $registration->online_id = $rawRegistration->online_id;
        $registration->registration_date = $registrationDate->value;
        $registration->semester_enrollment_id = $semesterEnrollment->id;
        $registration->source = RecordSource::PORTAL;

        $registration->save();

        return $registration;
    }

    public static function getUsingOnlineId(string $onlineId): self
    {
        return self::query()->where('online_id', $onlineId)->firstOrFail();
    }

    /** @return \Illuminate\Database\Eloquent\Relations\MorphMany<\App\Models\VettingReport, \App\Models\Registration> */
    public function vettable(): MorphMany
    {
        return $this->MorphMany(VettingReport::class, 'vettable');
    }

    /**
     * phpcs:ignore SlevomatCodingStandard.Files.LineLength
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<\App\Models\SemesterEnrollment, \App\Models\Registration>
     */
    public function semesterEnrollment(): BelongsTo
    {
        return $this->belongsTo(SemesterEnrollment::class);
    }

    /** @return \Illuminate\Database\Eloquent\Relations\BelongsTo<\App\Models\Course, \App\Models\Registration> */
    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    /** @return \Illuminate\Database\Eloquent\Relations\HasOne<\App\Models\Result, \App\Models\Registration> */
    public function result(): HasOne
    {
        return $this->hasOne(Result::class);
    }

    public function student(): Student
    {
        $student = $this->semesterEnrollment->enrollment->student;

        assert($student instanceof Student);

        return $student;
    }

    public function semester(): Semester
    {
        $semester = $this->semesterEnrollment->semester;

        assert($semester instanceof Semester);

        return $semester;
    }

    public function session(): Session
    {
        $session = $this->semesterEnrollment->sessionEnrollment->session;

        assert($session instanceof Session);

        return $session;
    }

    /**
     * phpcs:ignore SlevomatCodingStandard.Files.LineLength
     * @return array{course_status: 'App\Enums\CourseStatus', credit_unit: 'App\Enums\CreditUnit', registration_date: 'date', source: 'App\Enums\RecordSource' }
     */
    protected function casts(): array
    {
        return [
            'course_status' => CourseStatus::class,
            'credit_unit' => CreditUnit::class,
            'registration_date' => 'date',
            'source' => RecordSource::class,
        ];
    }
}
