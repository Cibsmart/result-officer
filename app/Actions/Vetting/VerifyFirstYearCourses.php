<?php

declare(strict_types=1);

namespace App\Actions\Vetting;

use App\Enums\CourseType;
use App\Enums\VettingStatus;
use App\Enums\VettingType;
use App\Enums\Year;
use App\Models\ProgramCurriculumLevel;
use App\Models\Student;
use Illuminate\Database\Eloquent\Collection;

final class VerifyFirstYearCourses extends ReportVettingStep
{
    public function execute(Student $student): VettingStatus
    {
        $this->createVettingStep($student, VettingType::CHECK_FIRST_YEAR_COURSES);

        $firstYearEnrollment = $student->sessionEnrollments()->where('year', Year::FIRST)->first();

        if ($firstYearEnrollment === null) {
            $this->report($student, '');

            return VettingStatus::UNCHECKED;
        }

        $programCurriculum = $student->programCurriculum();

        if ($programCurriculum === null) {
            return VettingStatus::UNCHECKED;
        }

        $firstYearProgramCurriculum = $programCurriculum->programCurriculumLevels()
            ->with('programCurriculumSemesters.programCurriculumCourses')
            ->orderBy('level_id')
            ->first();

        $firstYearRegistrations = $firstYearEnrollment->registrations;

        $passed = $this->checkFirstYearCourses($firstYearProgramCurriculum, $firstYearRegistrations);

        return $passed
            ? VettingStatus::PASSED
            : VettingStatus::FAILED;
    }

    /** @param \Illuminate\Database\Eloquent\Collection<int, \App\Models\Registration> $firstYearRegistrations */
    private function checkFirstYearCourses(
        ProgramCurriculumLevel $firstYearProgramCurriculum,
        Collection $firstYearRegistrations,
    ): bool {
        $passed = true;

        $firstYearProgramCurriculumSemesters = $firstYearProgramCurriculum->programCurriculumSemesters;

        foreach ($firstYearProgramCurriculumSemesters as $firstYearProgramCurriculumSemester) {
            $unregisteredFirstYearCourses = $firstYearProgramCurriculumSemester->programCurriculumCourses()
                ->whereNotIn('id', $firstYearRegistrations->pluck('program_curriculum_course_id'))
                ->with('course', 'programCurriculumSemester.semester')
                ->where('course_type', '<>', CourseType::ELECTIVE)
                ->get();

            foreach ($unregisteredFirstYearCourses as $unregisteredFirstYearCourse) {

                $courseType = $unregisteredFirstYearCourse->course_type;
                assert($courseType instanceof CourseType);

                $passed = false;

                $course = $unregisteredFirstYearCourse->course;
                $semester = $unregisteredFirstYearCourse->programCurriculumSemester->semester;
                $message = "{$course->code} - {$semester->name} semester ({$courseType->name})";

                $this->report($unregisteredFirstYearCourse, $message);
            }
        }

        return $passed;
    }
}
