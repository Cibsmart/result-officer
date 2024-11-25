<?php

declare(strict_types=1);

namespace App\Actions\Vetting;

use App\Enums\VettingStatus;
use App\Enums\VettingType;
use App\Models\Student;

final class VerifyCoursesCreditUnit extends ReportVettingStep
{
    public function execute(Student $student): VettingStatus
    {
        $this->createVettingStep($student, VettingType::CHECK_CREDIT_UNITS);

        /** @var \Illuminate\Support\Collection<int, \App\Models\Registration> $registrations */
        $registrations = $student->registrations()
            ->with('programCurriculumCourse', 'course')
            ->whereNotNull('program_curriculum_course_id')
            ->get();

        if ($registrations->isEmpty()) {
            $this->report($student, '');

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

            $message = "{$course->code} - {$session->name} {$semester->name} semester";

            $this->report($registration, $message);
        }

        return $passed
            ? VettingStatus::PASSED
            : VettingStatus::FAILED;
    }
}
