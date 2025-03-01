<?php

declare(strict_types=1);

namespace App\Models;

use App\Data\Models\RegistrationModelData;
use App\Enums\CourseStatus;
use App\Enums\CreditUnit;
use App\Enums\RecordSource;
use App\Enums\StudentStatus;
use Exception;
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

    public static function getUsingId(int $id): self
    {
        return self::query()->where('id', $id)->firstOrFail();
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

    /**
     * @param array{credit_unit?: int, in_course?: int, exam?: int} $newResult
     * @throws \Exception
     */
    public static function updateRegistrationAndResult(
        Student $student,
        self $registration,
        array $newResult,
    ): void {
        if (in_array($student->status, StudentStatus::archivedStates(), true)) {
            throw new Exception("Cannot update results of {$student->status->value} student");
        }

        if (array_key_exists('credit_unit', $newResult)) {
            $registration->credit_unit = CreditUnit::from($newResult['credit_unit']);
            $registration->save();

            self::checkAndRecomputeResult($student, $registration);
        }

        if (! array_key_exists('in_course', $newResult)
            && ! array_key_exists('exam', $newResult)) {
            return;
        }

        Result::updateResult($student, $registration, $newResult);
    }

    public static function checkAndRecomputeResult(Student $student, self $registration): void
    {
        $result = $registration->result;

        if ($result === null) {
            return;
        }

        $result->recompute($student);
    }

    /** @throws \Exception */
    public static function deleteRegistration(Student $student, self $registration): void
    {
        if (in_array($student->status, StudentStatus::archivedStates(), true)) {
            throw new Exception("Cannot delete results of {$student->status->value} student");
        }

        $registration->delete();
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

    public function getUpdateData(): string
    {
        $result = $this->result;
        $string = "{$this->credit_unit->value}";

        if ($result === null) {
            return "{$string}-0-0-0";
        }

        $scores = $result->getScores();

        return "{$string}-{$scores['in_course']}-{$scores['in_course_2']}-{$scores['exam']}";
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
