<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\CourseStatusEnum;
use App\Enums\CreditUnitEnum;
use App\Enums\RecordSource;
use App\Values\DateValue;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

final class CourseRegistration extends Model
{
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
    ): void {
        $registrationDate = DateValue::fromString($rawRegistration->registration_date);
        $registration = new self();

        $registration->course_id = $course->id;
        $registration->credit_unit = CreditUnitEnum::from((int) $rawRegistration->credit_unit)->value;
        $registration->course_status = CourseStatusEnum::FRESH;
        $registration->online_id = $rawRegistration->online_id;
        $registration->registration_date = $registrationDate->value;
        $registration->semester_enrollment_id = $semesterEnrollment->id;
        $registration->source = RecordSource::PORTAL;

        $registration->save();
    }

    public static function getUsingOnlineId(string $onlineId): self
    {
        return self::query()->where('online_id', $onlineId)->firstOrFail();
    }

    /**
     * phpcs:ignore SlevomatCodingStandard.Files.LineLength
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<\App\Models\SemesterEnrollment, \App\Models\CourseRegistration>
     */
    public function semesterEnrollment(): BelongsTo
    {
        return $this->belongsTo(SemesterEnrollment::class);
    }

    /** @return \Illuminate\Database\Eloquent\Relations\BelongsTo<\App\Models\Course, \App\Models\CourseRegistration> */
    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    /** @return \Illuminate\Database\Eloquent\Relations\HasOne<\App\Models\Result> */
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

    /**
     * @return array{course_status: 'App\Enums\CourseStatusEnum', registration_date: 'date',
     *     source: 'App\Enums\RecordSource' }
     */
    protected function casts(): array
    {
        return [
            'course_status' => CourseStatusEnum::class,
            'registration_date' => 'date',
            'source' => RecordSource::class,
        ];
    }
}
