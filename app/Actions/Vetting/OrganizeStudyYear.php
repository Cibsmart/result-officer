<?php

declare(strict_types=1);

namespace App\Actions\Vetting;

use App\Enums\VettingStatus;
use App\Enums\Year;
use App\Models\Student;
use App\Models\VettingStep;
use Illuminate\Support\Collection;

final class OrganizeStudyYear extends ReportVettingStep
{
    public function execute(Student $student, VettingStep $vettingStep): VettingStatus
    {
        $enrollments = $student->sessionEnrollments()->orderBy('session_id')->get();

        if ($this->enrollmentYearIsOrganized($enrollments)) {
            return VettingStatus::PASSED;
        }

        $this->createReport($student, $vettingStep, 'Student Study Years Re-Organized');

        $currentYear = Year::FIRST;

        foreach ($enrollments as $enrollment) {
            $enrollment->updateYear($currentYear);

            $currentYear = $currentYear->next();
        }

        return VettingStatus::PASSED;
    }

    /** @param \Illuminate\Support\Collection<int, \App\Models\SessionEnrollment> $enrollments */
    public function enrollmentYearIsOrganized(Collection $enrollments): bool
    {
        $sessionYear = $enrollments->pluck('year.value', 'session_id');

        $previousYear = 0;

        foreach ($sessionYear as $year) {
            if ($year <= $previousYear) {
                return false;
            }

            $previousYear = $year;
        }

        return true;
    }
}
