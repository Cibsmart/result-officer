<?php

declare(strict_types=1);

namespace App\Actions\Vetting;

use App\Enums\CourseType;
use App\Enums\VettingStatus;
use App\Models\ProgramCurriculum;
use App\Models\Student;
use App\Models\VettingStep;
use Illuminate\Database\Eloquent\Collection;

final class VerifyCoreCourses extends ReportVettingStep
{
    public function execute(Student $student, VettingStep $vettingStep): VettingStatus
    {
        $registrations = $student->registrations()
            ->whereNotNull('program_curriculum_course_id')
            ->get();

        if ($registrations->isEmpty()) {
            $message = "{$student->registration_number} Courses Not Checked\n";

            $this->createReport($student, $vettingStep, $message);

            return VettingStatus::UNCHECKED;
        }

        $programCurriculum = $student->programCurriculum();

        if (is_null($programCurriculum)) {
            return VettingStatus::UNCHECKED;
        }

        $passed = $this->checkNonElectiveCourses($programCurriculum, $registrations, $vettingStep);

        return $passed
            ? VettingStatus::PASSED
            : VettingStatus::FAILED;
    }

    /** @param \Illuminate\Database\Eloquent\Collection<int, \App\Models\Registration> $registrations */
    private function checkNonElectiveCourses(
        ProgramCurriculum $programCurriculum,
        Collection $registrations,
        VettingStep $vettingStep,
    ): bool {
        /**
         * phpcs:ignore SlevomatCodingStandard.Files.LineLength
         * @var \Illuminate\Database\Eloquent\Collection<int, \App\Models\ProgramCurriculumCourse> $programCurriculumCourses
         */
        $programCurriculumCourses = $programCurriculum->programCurriculumCourses()
            ->where('course_type', '<>', CourseType::ELECTIVE)
            ->get();

        $passed = true;

        foreach ($programCurriculumCourses as $programCurriculumCourse) {
            if ($registrations->firstWhere('program_curriculum_course_id', $programCurriculumCourse->id)) {
                continue;
            }

            $passed = false;

            $message = "{$programCurriculumCourse->course->name} Not Taken\n";

            $this->createReport($programCurriculumCourse, $vettingStep, $message);
        }

        return $passed;
    }
}
