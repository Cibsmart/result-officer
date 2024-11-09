<?php

declare(strict_types=1);

namespace App\Actions\Vetting;

use App\Enums\VettingStatus;
use App\Models\Student;
use App\Models\VettingStep;

final class VerifyCoursesCreditUnit extends ReportVettingStep
{
    public function execute(Student $student, VettingStep $vettingStep): VettingStatus
    {
        /** @var \Illuminate\Support\Collection<int, \App\Models\Registration> $registrations */
        $registrations = $student->registrations()
            ->with('programCurriculumCourse', 'course')
            ->whereNotNull('program_curriculum_course_id')
            ->get();

        if ($registrations->isEmpty()) {
            $message = "Not Checked for {$student->registration_number}: ";
            $message .= "Unmatched Courses with program curriculum course  \n";

            $this->createReport($student, $vettingStep, $message);

            return VettingStatus::UNCHECKED;
        }

        $passed = true;

        foreach ($registrations as $registration) {
            $programCurriculumCourse = $registration->programCurriculumCourse;

            if ($registration->credit_unit === $programCurriculumCourse->credit_unit) {
                continue;
            }

            $passed = false;
            $course = $registration->course;
            $session = $registration->session();
            $semester = $registration->semester();

            $message = "Credit for {$course->code} in {$session->name} {$semester->name} Semester ";
            $message .= "does not matched with the program curriculum course credit unit  \n";

            $this->createReport($registration, $vettingStep, $message);
        }

        return $passed
            ? VettingStatus::PASSED
            : VettingStatus::FAILED;
    }
}
