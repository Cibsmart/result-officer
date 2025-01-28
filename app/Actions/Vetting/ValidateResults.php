<?php

declare(strict_types=1);

namespace App\Actions\Vetting;

use App\Enums\VettingStatus;
use App\Enums\VettingType;
use App\Models\Result;
use App\Models\SemesterEnrollment;
use App\Models\Session;
use App\Models\Student;

final class ValidateResults extends ReportVettingStep
{
    public function execute(Student $student): VettingStatus
    {
        $this->createVettingStep($student, VettingType::VALIDATE_RESULTS);

        $sessionEnrollments = $student->sessionEnrollments()
            ->whereHas('semesterEnrollments.registrations.result')
            ->with(['session', 'semesterEnrollments.semester'])
            ->get();

        $passed = true;
        $status = VettingStatus::UNCHECKED;

        foreach ($sessionEnrollments as $sessionEnrollment) {
            $status = VettingStatus::PASSED;

            $semesterEnrollments = $sessionEnrollment->semesterEnrollments;

            foreach ($semesterEnrollments as $semesterEnrollment) {
                $session = $sessionEnrollment->session;

                $passed = $this->validate($semesterEnrollment, $session) && $passed;
            }
        }

        return $passed
            ? $status
            : VettingStatus::FAILED;
    }

    private function validate(
        SemesterEnrollment $semesterEnrollment,
        Session $session,
    ): bool {
        $registrations = $semesterEnrollment->registrations()
            ->whereHas('result')
            ->with('result.resultDetail', 'course')
            ->get();

        $passed = true;

        foreach ($registrations as $registration) {
            $result = $registration->result;

            if ($this->passCheck($result)) {
                continue;
            }

            $passed = false;

            $code = $registration->course->code;
            $semester = $semesterEnrollment->semester;

            $message = "{$code} - {$session->name} {$semester->name} semester";

            $this->report($result, $message);
        }

        return $passed;
    }

    private function passCheck(Result $result): bool
    {
        $resultData = $result->getData();
        $resultDetail = $result->resultDetail;
        $resultDetailValue = $resultDetail->value;

        return $resultData === $resultDetailValue;
    }
}
