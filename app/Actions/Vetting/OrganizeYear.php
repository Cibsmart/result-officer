<?php

declare(strict_types=1);

namespace App\Actions\Vetting;

use App\Enums\Year;
use App\Models\Student;
use Illuminate\Support\Collection;

final class OrganizeYear
{
    public function execute(Student $student): void
    {
        $enrollments = $student->enrollments()->orderBy('session_id')->get();

        if ($this->enrollmentYearIsOrganized($enrollments)) {
            return;
        }

        $currentYear = Year::FIRST;

        foreach ($enrollments as $enrollment) {
            $enrollment->updateYear($currentYear);

            $currentYear = $currentYear->next();
        }
    }

    /** @param \Illuminate\Support\Collection<int, \App\Models\Enrollment> $enrollments */
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
