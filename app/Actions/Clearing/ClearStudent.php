<?php

declare(strict_types=1);

namespace App\Actions\Clearing;

use App\Enums\StudentStatus;
use App\Models\SemesterEnrollment;
use App\Models\SessionEnrollment;
use App\Models\StatusChangeEvent;
use App\Models\Student;
use Exception;

final class ClearStudent
{
    /** @throws \Exception */
    public function execute(Student $student): void
    {
        if (! $student->canBeCleared()) {
            throw new Exception('Student has not been vetted and cannot be cleared');
        }

        $this->updateFCGPA($student);

        StatusChangeEvent::recordStudentStatusChange($student, StudentStatus::CLEARED);

        $student->updateStatus(StudentStatus::CLEARED);
    }

    private function updateFCGPA(Student $student): void
    {
        $student->sessionEnrollments()
            ->with('semesterEnrollments.registrations')
            ->each(function (SessionEnrollment $sessionEnrollment): void {

                $sessionEnrollment->semesterEnrollments
                    ->each(function (SemesterEnrollment $semesterEnrollment): void {
                        $semesterEnrollment->updateSumsAndAverage();
                    });

                $sessionEnrollment->updateCountSumAndAverages();
            });

        $student->updateCountSumAndAverages();
    }
}
