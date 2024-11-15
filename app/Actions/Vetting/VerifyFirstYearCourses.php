<?php

declare(strict_types=1);

namespace App\Actions\Vetting;

use App\Enums\CourseType;
use App\Enums\VettingStatus;
use App\Enums\Year;
use App\Models\Student;
use App\Models\VettingStep;

final class VerifyFirstYearCourses extends ReportVettingStep
{
    public function execute(Student $student, VettingStep $vettingStep): VettingStatus
    {
        $firstYearEnrollment = $student->sessionEnrollments()->where('year', Year::FIRST)->first();

        if ($firstYearEnrollment === null) {
            $message = "First Year Courses not checked for {$student->registration_number}\n";

            $this->createReport($student, $vettingStep, $message);

            return VettingStatus::UNCHECKED;
        }

        $programCurriculum = $student->programCurriculum();

        if (is_null($programCurriculum)) {
            return VettingStatus::UNCHECKED;
        }

        $firstYearProgramCurriculum = $programCurriculum->programCurriculumLevels()
            ->with('programCurriculumSemesters.programCurriculumCourses')
            ->orderBy('level_id')
            ->first();

        $programCurriculumSemesters = $firstYearProgramCurriculum->programCurriculumSemesters;
        $firstYearRegistrations = $firstYearEnrollment->registrations;

        $passed = true;

        foreach ($programCurriculumSemesters as $programCurriculumSemester) {
            $unregisteredFirstYearCourses = $programCurriculumSemester->programCurriculumCourses()
                ->whereNotIn('id', $firstYearRegistrations->pluck('program_curriculum_course_id'))
                ->where('course_type', '<>', CourseType::ELECTIVE)
                ->get();

            foreach ($unregisteredFirstYearCourses as $unregisteredFirstYearCourse) {

                $courseType = $unregisteredFirstYearCourse->course_type;
                assert($courseType instanceof CourseType);

                $passed = false;

                $course = $unregisteredFirstYearCourse->course;
                $message = "{$course->code} ({$courseType->name}) Not taken in the first year \n";

                $this->createReport($unregisteredFirstYearCourse, $vettingStep, $message);
            }
        }

        return $passed
            ? VettingStatus::PASSED
            : VettingStatus::FAILED;
    }
}
