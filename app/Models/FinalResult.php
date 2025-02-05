<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\CourseStatus;
use App\Enums\CreditUnit;
use App\Enums\RecordSource;
use Illuminate\Database\Eloquent\Model;

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
