<?php

declare(strict_types=1);

namespace App\Actions\Vetting;

use App\Enums\Year;
use App\Models\Student;
use Illuminate\Support\Collection;

final class OrganizeYear
{
    private string $report = '';

    public function execute(Student $student): void
    {
        $enrollments = $student->sessionEnrollments()->orderBy('session_id')->get();

        if ($this->enrollmentYearIsOrganized($enrollments)) {
            return;
        }

        $currentYear = Year::FIRST;

        foreach ($enrollments as $enrollment) {
            $enrollment->updateYear($currentYear);

            $currentYear = $currentYear->next();
        }

        $this->report = 'RE-ORGANIZED STUDY YEARS';
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

    public function report(): string
    {
        return $this->report === ''
            ? 'PASSED'
            : $this->report;
    }
}
