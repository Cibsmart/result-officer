<?php

declare(strict_types=1);

namespace App\Actions\Vetting;

use App\Enums\CreditUnit;
use App\Models\SemesterEnrollment;

final class CheckMinMaxCreditUnit
{
    private string $report = '';

    public function execute(SemesterEnrollment $semesterEnrollment): void
    {
        $creditUnitSum = $semesterEnrollment->registrations()->sum('credit_unit');
        $semester = $semesterEnrollment->semester->name;
        $session = $semesterEnrollment->sessionEnrollment->session->name;

        $minCreditUnit = CreditUnit::minSemesterUnit()->value;
        $maxCreditUnit = CreditUnit::maxSemesterUnit()->value;

        if ($creditUnitSum < $minCreditUnit) {
            $this->report .= "{$session} {$semester} Semester is less than the minimum {$minCreditUnit} Credit Unit \n";
        }

        if ($creditUnitSum <= $maxCreditUnit) {
            return;
        }

        $this->report .= "{$session} {$semester} Semester is greater than the maximum {$maxCreditUnit} Credit Unit \n";
    }

    public function report(): string
    {
        return $this->report === ''
            ? 'PASSED'
            : $this->report;
    }
}
