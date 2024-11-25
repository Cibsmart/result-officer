<?php

declare(strict_types=1);

namespace App\Actions\Vetting;

use App\Data\Query\StudentCoursesData;
use App\Enums\VettingStatus;
use App\Enums\VettingType;
use App\Models\Registration;
use App\Models\Student;
use App\Queries\StudentCourses;

final class VerifyCoursesCreditUnit extends ReportVettingStep
{
    public function execute(Student $student): VettingStatus
    {
        $this->createVettingStep($student, VettingType::CHECK_CREDIT_UNITS);

        $registrations = StudentCourses::for($student)->query()
            ->whereNotNull('program_curriculum_course_id')
            ->get();

        $registrations = StudentCoursesData::collect($registrations);

        if ($registrations->isEmpty()) {
            return VettingStatus::UNCHECKED;
        }

        $passed = true;

        foreach ($registrations as $registration) {
            $registrationModel = Registration::query()
                ->where('id', $registration->registrationId)
                ->with('programCurriculumCourse', 'course')
                ->first();

            $programCourse = $registrationModel->programCurriculumCourse;

            if ($registrationModel->credit_unit === $programCourse->credit_unit) {
                continue;
            }

            $passed = false;
            $courseCode = $registration->courseCode;
            $session = $registration->session;
            $semester = $registration->semester;

            $message = "{$courseCode} - {$session} {$semester} semester";

            $this->report($registrationModel, $message);
        }

        return $passed
            ? VettingStatus::PASSED
            : VettingStatus::FAILED;
    }
}
