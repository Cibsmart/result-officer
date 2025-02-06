<?php

declare(strict_types=1);

namespace App\Actions\Clearing;

use App\Enums\StudentStatus;
use App\Models\FinalResult;
use App\Models\FinalSemesterEnrollment;
use App\Models\FinalSessionEnrollment;
use App\Models\FinalStudent;
use App\Models\SemesterEnrollment;
use App\Models\SessionEnrollment;
use App\Models\StatusChangeEvent;
use App\Models\Student;
use Exception;

final class ClearStudent
{
    /** @throws \Exception */
    public function execute(Student $student, FinalStudent $finalStudent): void
    {
        if (! $student->canBeCleared()) {
            throw new Exception('Student has not been vetted and cannot be cleared');
        }

        $this->archiveResultsFor($student, $finalStudent);

        StatusChangeEvent::recordStudentStatusChange($student, StudentStatus::CLEARED);

        $student->updateStatus(StudentStatus::CLEARED);
    }

    private function archiveResultsFor(Student $student, FinalStudent $finalStudent): void
    {
        $sessionEnrollments = $student->sessionEnrollments()
            ->with('semesterEnrollments.registrations.course')
            ->orderBy('year')
            ->get();

        foreach ($sessionEnrollments as $sessionEnrollment) {
            $finalSessionEnrollment = FinalSessionEnrollment::fromSessionEnrollment(
                sessionEnrollment: $sessionEnrollment,
                finalStudent: $finalStudent,
            );

            $this->archiveSessionResults($sessionEnrollment, $finalSessionEnrollment);

            $finalSessionEnrollment->updateCountSumAndAverages();
        }

        $finalStudent->updateCountSumAndAverages();
    }

    private function archiveSessionResults(
        SessionEnrollment $sessionEnrollment,
        FinalSessionEnrollment $finalSessionEnrollment,
    ): void {
        $semesterEnrollments = $sessionEnrollment->semesterEnrollments;

        foreach ($semesterEnrollments as $semesterEnrollment) {
            $finalSemesterEnrollment = FinalSemesterEnrollment::fromSemesterEnrollment(
                semesterEnrollment: $semesterEnrollment,
                finalSessionEnrollment: $finalSessionEnrollment,
            );

            $this->archiveSemesterResults($semesterEnrollment, $finalSemesterEnrollment);

            $finalSemesterEnrollment->updateSumsAndAverage();
        }
    }

    private function archiveSemesterResults(
        SemesterEnrollment $semesterEnrollment,
        FinalSemesterEnrollment $finalSemesterEnrollment,
    ): void {
        $registrations = $semesterEnrollment->registrations;

        foreach ($registrations as $registration) {
            FinalResult::fromRegistration($registration, $finalSemesterEnrollment);
        }
    }
}
