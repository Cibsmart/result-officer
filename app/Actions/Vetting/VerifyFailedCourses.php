<?php

declare(strict_types=1);

namespace App\Actions\Vetting;

use App\Enums\Grade;
use App\Enums\VettingStatus;
use App\Models\Registration;
use App\Models\Student;
use App\Models\VettingStep;
use App\Queries\StudentCourses;

final class VerifyFailedCourses extends ReportVettingStep
{
    public function execute(Student $student, VettingStep $vettingStep): VettingStatus
    {
        //        $this->createVettingStep($student, VettingType::CHECK_FAILED_COURSES):

        $results = StudentCourses::for($student)->get();

        if ($results->isEmpty()) {
            $message = "Failed courses not checked for {$student->registration_number}\n";

            $this->createReport($student, $vettingStep, $message);

            return VettingStatus::UNCHECKED;
        }

        $failedCourses = [];

        foreach ($results as $result) {
            if ($result->grade === Grade::F->value || $result->grade === null) {
                $failedCourses[$result->course_id] = $result;

                continue;
            }

            if (
                ! array_key_exists($result->course_id, $failedCourses)
                || ($result->session_id <= $failedCourses[$result->course_id]->session_id)
            ) {
                continue;
            }

            unset($failedCourses[$result->course_id]);
        }

        $passed = true;

        foreach ($failedCourses as $failedCourse) {
            $message = "Failed {$failedCourse->course_code} in {$failedCourse->session} {$failedCourse->semester}";
            $message .= " Semester\n";

            $registration = Registration::query()->find($failedCourse->registration_id);

            $this->createReport($registration, $vettingStep, $message);

            $passed = false;
        }

        return $passed
            ? VettingStatus::PASSED
            : VettingStatus::FAILED;
    }
}
