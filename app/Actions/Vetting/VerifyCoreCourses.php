<?php

declare(strict_types=1);

namespace App\Actions\Vetting;

use App\Enums\CourseType;
use App\Enums\VettingStatus;
use App\Enums\VettingType;
use App\Models\ProgramCurriculum;
use App\Models\Student;
use Illuminate\Database\Eloquent\Collection;

final class VerifyCoreCourses extends ReportVettingStep
{
    public function execute(Student $student): VettingStatus
    {
        $this->createVettingStep($student, VettingType::CHECK_CORE_COURSES);

        /** @var \Illuminate\Database\Eloquent\Collection<int, \App\Models\Registration> $registrations */
        $registrations = $student->registrations()
            ->whereNotNull('program_curriculum_course_id')
            ->get();

        if ($registrations->isEmpty()) {
            return VettingStatus::UNCHECKED;
        }

        $programCurriculum = $student->programCurriculum();

        $passed = $this->checkNonElectiveCourses($programCurriculum, $registrations);

        return $passed
            ? VettingStatus::PASSED
            : VettingStatus::FAILED;
    }

    /** @param \Illuminate\Database\Eloquent\Collection<int, \App\Models\Registration> $registrations */
    private function checkNonElectiveCourses(
        ProgramCurriculum $programCurriculum,
        Collection $registrations,
    ): bool {
        /**
         * phpcs:ignore SlevomatCodingStandard.Files.LineLength
         * @var \Illuminate\Database\Eloquent\Collection<int, \App\Models\ProgramCurriculumCourse> $programCurriculumCourses
         */
        $programCurriculumCourses = $programCurriculum->programCurriculumCourses()
            ->with('course', 'programCurriculumSemester.semester',
                'programCurriculumSemester.programCurriculumLevel.level')
            ->where('course_type', '<>', CourseType::ELECTIVE)
            ->get();

        $passed = true;

        foreach ($programCurriculumCourses as $programCurriculumCourse) {
            if ($registrations->firstWhere('program_curriculum_course_id', $programCurriculumCourse->id)) {
                continue;
            }

            $passed = false;

            $course = $programCurriculumCourse->course;
            $courseType = $programCurriculumCourse->course_type;
            $semester = $programCurriculumCourse->programCurriculumSemester->semester;
            $level = $programCurriculumCourse->programCurriculumSemester->programCurriculumLevel->level;

            $message = "{$course->code} ({$courseType->value}) - {$level->name} Level {$semester->name} Semester";

            $this->report($programCurriculumCourse, $message);
        }

        return $passed;
    }
}
