<?php

declare(strict_types=1);

namespace App\Actions\Vetting;

use App\Enums\CreditUnit;
use App\Enums\VettingStatus;
use App\Enums\VettingType;
use App\Models\SemesterEnrollment;
use App\Models\Session;
use App\Models\Student;

final class VerifySemesterCreditLimits extends ReportVettingStep
{
    public function execute(Student $student): VettingStatus
    {
        $this->createVettingStep($student, VettingType::CHECK_SEMESTER_CREDIT_LOADS);

        $sessionEnrollments = $student->sessionEnrollments()
            ->with(['session', 'semesterEnrollments.semester', 'semesterEnrollments.programCurriculumSemester'])
            ->get();

        $passed = true;
        $status = VettingStatus::UNCHECKED;

        foreach ($sessionEnrollments as $sessionEnrollment) {
            $status = VettingStatus::PASSED;

            $semesterEnrollments = $sessionEnrollment->semesterEnrollments;

            $session = $sessionEnrollment->session;

            foreach ($semesterEnrollments as $semesterEnrollment) {
                $passed = $this->performSemesterCreditUnitCheck($semesterEnrollment, $session) && $passed;
            }
        }

        return $passed
            ? $status
            : VettingStatus::FAILED;
    }

    public function performSemesterCreditUnitCheck(
        SemesterEnrollment $semesterEnrollment,
        Session $session,
    ): bool {
        $creditUnitSum = $semesterEnrollment->registrations()->sum('credit_unit');

        $programSemester = $semesterEnrollment->programCurriculumSemester;

        $semester = $semesterEnrollment->semester;

        $minSemesterTotalCreditUnit = $programSemester
            ? $programSemester->minimum_credit_units
            : CreditUnit::minSemesterUnit()->value;

        $maxSemesterTotalCreditUnit = $programSemester
            ? $programSemester->maximum_credit_units
            : CreditUnit::maxSemesterUnit()->value;

        $minCheck = $creditUnitSum >= $minSemesterTotalCreditUnit;
        $maxCheck = $creditUnitSum <= $maxSemesterTotalCreditUnit;

        if ($minCheck && $maxCheck) {
            return true;
        }

        $message = "{$session->name} {$semester->name} semester ({$creditUnitSum}";

        $message .= $minCheck
            ? " exceeds {$maxSemesterTotalCreditUnit} units)"
            : " below {$minSemesterTotalCreditUnit} units)";

        $this->report($semesterEnrollment, $message);

        return false;
    }
}
