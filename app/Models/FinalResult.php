<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\CourseStatus;
use App\Enums\CreditUnit;
use App\Enums\Grade;
use App\Enums\RecordSource;
use App\Values\DateValue;
use App\Values\ExamScore;
use App\Values\InCourseScore;
use App\Values\RegistrationNumber;
use App\Values\SessionValue;
use App\Values\TotalScore;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class FinalResult extends Model
{
    public static function fromRegistration(
        Registration $registration,
        FinalSemesterEnrollment $finalSemesterEnrollment,
    ): self {
        $course = $registration->course;
        assert($course instanceof Course);

        $finalCourse = FinalCourse::getOrCreateFromCourse($course);
        $result = $registration->result;

        $finalResult = new self();

        $finalResult->final_semester_enrollment_id = $finalSemesterEnrollment->id;
        $finalResult->final_course_id = $finalCourse->id;
        $finalResult->course_status = $registration->course_status;
        $finalResult->credit_unit = $registration->credit_unit;
        $finalResult->scores = $result->scores;
        $finalResult->total_score = $result->total_score;
        $finalResult->grade = $result->grade;
        $finalResult->grade_point = $result->grade_point;
        $finalResult->remarks = $result->remarks;
        $finalResult->source = RecordSource::SYSTEM;
        $finalResult->lecturer_id = $result->lecturer_id;

        $finalResult->save();

        return $finalResult;
    }

    public static function createFromRawFinalResult(
        FinalSemesterEnrollment $finalSemesterEnrollment,
        RawFinalResult $result,
        FinalCourse $finalCourse,
        CourseStatus $status,
        ?Registration $registration,
    ): self {
        $registrationNumber = RegistrationNumber::new($result->registration_number);
        $creditUnit = CreditUnit::from($result->credit_unit);
        $inCourse = InCourseScore::new($result->in_course);
        $exam = ExamScore::new($result->exam);
        $total = TotalScore::fromInCourseAndExam($inCourse, $exam);
        $total2 = TotalScore::new($result->total);
        $grade2 = Grade::from($result->grade);
        $grade = $total->grade(
            $registrationNumber->allowEGrade() || $finalSemesterEnrollment->session()->allowsEGrade(),
        );

        self::checkAndReportTotalDifference($total, $total2, $result);
        self::checkAndReportGradeDifference($grade, $grade2, $result);

        $session = self::getResultOriginalSession($result);

        $lecturer = self::getLecturer($result);

        $finalResult = new self();
        $finalResult->final_semester_enrollment_id = $finalSemesterEnrollment->id;
        $finalResult->final_course_id = $finalCourse->id;
        $finalResult->course_status = $status;
        $finalResult->credit_unit = $creditUnit;
        $finalResult->total_score = $total->value;
        $finalResult->scores = ['in_course' => $inCourse->value, 'exam' => $exam->value];
        $finalResult->grade = $grade;
        $finalResult->grade_point = $grade->point() * $creditUnit->value;
        $finalResult->source = RecordSource::EXCEL;
        $finalResult->exam_date = DateValue::fromValue($result->exam_date)->value;
        $finalResult->lecturer_id = $lecturer;
        $finalResult->original_session_id = $session;
        $finalResult->registration_id = $registration
            ? $registration->id
            : null;

        $finalResult->save();

        return $finalResult;
    }

    public static function checkAndReportTotalDifference(
        TotalScore $total,
        TotalScore $total2,
        RawFinalResult $result,
    ): void {
        if ($total->value === $total2->value) {
            return;
        }

        $result->message .= "Total score mismatch: Computed: {$total->value}, Excel: {$total2->value}";
        $result->save();
    }

    public static function checkAndReportGradeDifference(
        Grade $grade,
        Grade $grade2,
        RawFinalResult $result,
    ): void {
        if ($grade->value === $grade2->value) {
            return;
        }

        $result->message .= "Grade mismatch. Computed: {$grade->value}, Excel: {$grade2->value}";
        $result->save();
    }

    /** @return \Illuminate\Database\Eloquent\Relations\BelongsTo<\App\Models\FinalCourse, \App\Models\FinalResult> */
    public function finalCourse(): BelongsTo
    {
        return $this->belongsTo(FinalCourse::class);
    }

    private static function getResultOriginalSession(RawFinalResult $result): ?int
    {
        $session = null;

        if ($result->originating_session) {
            $session = SessionValue::new($result->session);
            $sessionOriginal = SessionValue::new($result->originating_session);

            $session = $session->value === $sessionOriginal->value
                ? null
                : $session->getSession()->id;
        }

        return $session;
    }

    private static function getLecturer(RawFinalResult $result): ?int
    {
        return $result->examiner !== null
            ? Lecturer::getOrCreateUsingName($result->examiner, $result->examiner_department)->id
            : null;
    }

    /**
     * phpcs:ignore SlevomatCodingStandard.Files.LineLength
     * @return array{course_status: 'App\Enums\CourseStatus', credit_unit: 'App\Enums\CreditUnit', scores: 'json', source: 'App\Enums\RecordSource'}
     */
    protected function casts(): array
    {
        return [
            'course_status' => CourseStatus::class,
            'credit_unit' => CreditUnit::class,
            'scores' => 'json',
            'source' => RecordSource::class,
        ];
    }
}
