<?php

declare(strict_types=1);

namespace App\Models;

use App\Data\Models\RegistrationModelData;
use App\Enums\CourseStatus;
use App\Enums\CreditUnit;
use App\Enums\RecordSource;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Collection;

final class Registration extends Model
{
    use SoftDeletes;

    protected $with = ['result'];

    public static function createFromRawRegistration(
        RawRegistration $rawRegistration,
        SemesterEnrollment $semesterEnrollment,
        Course $course,
    ): self {
        $registration = RegistrationModelData::fromRawRegistration($rawRegistration, $semesterEnrollment, $course)
            ->getModel();

        $registration->save();

        return $registration;
    }

    public static function createFromLegacyResult(
        SemesterEnrollment $semesterEnrollment,
        LegacyResult|LegacyFinalResult $legacyResult,
        CourseStatus $courseStatus,
    ): self {
        $registration = RegistrationModelData::fromLegacyResult($semesterEnrollment, $legacyResult, $courseStatus)
            ->getModel();

        $registration->save();

        return $registration;
    }

    public static function getUsingOnlineId(string $onlineId): self
    {
        return self::query()->where('online_id', $onlineId)->firstOrFail();
    }

    /** @param \Illuminate\Support\Collection<int, int> $registrationIds */
    public static function updateCurriculumCourseId(
        Collection $registrationIds,
        ProgramCurriculumCourse $programCourseModel,
    ): void {
        self::query()
            ->whereIn('id', $registrationIds)
            ->whereNull('program_curriculum_course_id')
            ->update(['program_curriculum_course_id' => $programCourseModel->id]);
    }

    /** @return \Illuminate\Database\Eloquent\Relations\MorphMany<\App\Models\VettingReport, \App\Models\Registration> */
    public function vettingReports(): MorphMany
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

    /**
     * phpcs:ignore SlevomatCodingStandard.Files.LineLength
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<\App\Models\ProgramCurriculumCourse, \App\Models\Registration>
     */
    public function programCurriculumCourse(): BelongsTo
    {
        return $this->belongsTo(ProgramCurriculumCourse::class);
    }

    public function student(): Student
    {
        $student = $this->semesterEnrollment->sessionEnrollment->student;

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
