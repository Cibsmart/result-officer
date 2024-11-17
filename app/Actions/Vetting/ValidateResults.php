<?php

declare(strict_types=1);

namespace App\Actions\Vetting;

use App\Enums\VettingStatus;
use App\Enums\VettingType;
use App\Models\SemesterEnrollment;
use App\Models\Session;
use App\Models\Student;
use Illuminate\Support\Facades\Hash;

final class ValidateResults extends ReportVettingStep
{
    public function execute(Student $student): VettingStatus
    {
        $this->createVettingStep($student, VettingType::VALIDATE_RESULTS);

        $sessionEnrollments = $student->sessionEnrollments()->with(['session', 'semesterEnrollments.semester'])->get();

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
        $registrations = $semesterEnrollment->registrations()->with('result', 'course')->get();

        $passed = true;

        foreach ($registrations as $registration) {
            $result = $registration->result;

            if (Hash::check($result->getData(), $result->data)) {
                continue;
            }

            $code = $registration->course->code;
            $semester = $semesterEnrollment->semester;

            $passed = false;
            $message = "{$code} in {$semester->name} semester {$session->name} is invalid. \n";

            $this->createReport($result, $message);
        }

        return $passed;
    }
}
