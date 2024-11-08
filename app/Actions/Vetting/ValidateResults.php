<?php

declare(strict_types=1);

namespace App\Actions\Vetting;

use App\Enums\VettingStatus;
use App\Models\Registration;
use App\Models\Result;
use App\Models\SemesterEnrollment;
use App\Models\Session;
use App\Models\Student;
use App\Models\VettingReport;
use App\Models\VettingStep;
use Illuminate\Support\Facades\Hash;

use function PHPUnit\Framework\assertNotNull;

final class ValidateResults extends ReportVettingStep
{
    public function execute(Student $student, VettingStep $vettingStep): VettingStatus
    {
        $sessionEnrollments = $student->sessionEnrollments()->with(['session', 'semesterEnrollments.semester'])->get();

        $passed = true;
        $status = VettingStatus::UNCHECKED;

        foreach ($sessionEnrollments as $sessionEnrollment) {
            $status = VettingStatus::PASSED;

            $semesterEnrollments = $sessionEnrollment->semesterEnrollments;

            foreach ($semesterEnrollments as $semesterEnrollment) {
                $session = $sessionEnrollment->session;

                assertNotNull($session);

                $passed = $this->validate($semesterEnrollment, $session, $vettingStep) && $passed;
            }
        }

        return $passed
            ? $status
            : VettingStatus::FAILED;
    }

    private function validate(
        SemesterEnrollment $semesterEnrollment,
        Session $session,
        VettingStep $vettingStep,
    ): bool {
        $registrations = $semesterEnrollment->registrations()->with('result', 'course')->get();

        $passed = true;

        foreach ($registrations as $registration) {
            $result = $registration->result;

            if (Hash::check($result->getData(), $result->data)) {
                continue;
            }

            assertNotNull($result);

            $passed = false;

            $this->createReport($result, $vettingStep, $registration, $semesterEnrollment, $session);
        }

        return $passed;
    }

    private function createReport(
        Result $result,
        VettingStep $vettingStep,
        Registration $registration,
        SemesterEnrollment $semesterEnrollment,
        Session $session,
    ): void {
        VettingReport::updateOrCreateUsingModel($result, $vettingStep, VettingStatus::FAILED);

        $code = $registration->course->code;
        $semester = $semesterEnrollment->semester;

        $this->updateReport("{$code} in {$semester->name} semester {$session->name} is invalid. \n");
    }
}
