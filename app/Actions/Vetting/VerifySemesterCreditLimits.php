<?php

declare(strict_types=1);

namespace App\Actions\Vetting;

use App\Enums\CreditUnit;
use App\Enums\VettingStatus;
use App\Models\SemesterEnrollment;
use App\Models\Session;
use App\Models\Student;
use App\Models\VettingStep;

final class VerifySemesterCreditLimits extends ReportVettingStep
{
    public function execute(Student $student, VettingStep $vettingStep): VettingStatus
    {
        $sessionEnrollments = $student->sessionEnrollments()->with(['session', 'semesterEnrollments.semester'])->get();

        $passed = true;
        $status = VettingStatus::UNCHECKED;

        foreach ($sessionEnrollments as $sessionEnrollment) {
            $status = VettingStatus::PASSED;

            $semesterEnrollments = $sessionEnrollment->semesterEnrollments;

            $session = $sessionEnrollment->session;

            foreach ($semesterEnrollments as $semesterEnrollment) {
                $passed = $this->performSemesterCreditUnitCheck($semesterEnrollment, $vettingStep, $session) && $passed;
            }
        }

        return $passed
            ? $status
            : VettingStatus::FAILED;
    }

    public function performSemesterCreditUnitCheck(
        SemesterEnrollment $semesterEnrollment,
        VettingStep $vettingStep,
        Session $session,
    ): bool {
        $creditUnitSum = $semesterEnrollment->registrations()->sum('credit_unit');

        $semester = $semesterEnrollment->semester;

        $minSemesterTotalCreditUnit = CreditUnit::minSemesterUnit()->value;
        $maxSemesterTotalCreditUnit = CreditUnit::maxSemesterUnit()->value;

        $minCheck = $creditUnitSum >= $minSemesterTotalCreditUnit;
        $maxCheck = $creditUnitSum <= $maxSemesterTotalCreditUnit;

        if ($minCheck && $maxCheck) {
            return true;
        }

        $message = "{$session->name} {$semester->name} Semester is ";

        $message .= $minCheck
            ? "greater than the maximum {$maxSemesterTotalCreditUnit} Credit Units \n"
            : "less than the minimum {$minSemesterTotalCreditUnit} Credit Units \n";

        $this->createReport($semesterEnrollment, $vettingStep, $message);

        return false;
    }
}
