<?php

declare(strict_types=1);

namespace App\Actions\Vetting;

use App\Data\Query\ProgramCoursesData;
use App\Data\Query\StudentCoursesData;
use App\Enums\CourseType;
use App\Enums\VettingStatus;
use App\Enums\VettingType;
use App\Models\ProgramCurriculum;
use App\Models\ProgramCurriculumCourse;
use App\Models\Student;
use App\Queries\ProgramCourses;
use App\Queries\StudentCourses;
use Illuminate\Support\Collection;

final class VerifyCoreCourses extends ReportVettingStep
{
    public function execute(Student $student): VettingStatus
    {
        $this->createVettingStep($student, VettingType::CHECK_CORE_COURSES);

        $registrations = StudentCourses::for($student)->query()
            ->whereNotNull('program_curriculum_course_id')
            ->get();

        $registrations = StudentCoursesData::collect($registrations);

        if ($registrations->isEmpty()) {
            return VettingStatus::UNCHECKED;
        }

        $programCurriculum = $student->programCurriculum();

        $passed = $this->checkNonElectiveCourses($programCurriculum, $registrations);

        return $passed
            ? VettingStatus::PASSED
            : VettingStatus::FAILED;
    }

    /** @param \Illuminate\Support\Collection<int, \App\Data\Query\StudentCoursesData> $registrations */
    private function checkNonElectiveCourses(
        ProgramCurriculum $programCurriculum,
        Collection $registrations,
    ): bool {

        $nonElectiveProgramCourses = ProgramCourses::for($programCurriculum)->query()
            ->where('course_type', '<>', CourseType::ELECTIVE)
            ->get();
        $nonElectiveProgramCourses = ProgramCoursesData::collect($nonElectiveProgramCourses);

        $passed = true;

        foreach ($nonElectiveProgramCourses as $nonElectiveProgramCourse) {
            $attempted = $registrations->where('programCurriculumCourseId', $nonElectiveProgramCourse->programCourseId);

            if ($attempted->isNotEmpty()) {
                continue;
            }

            $passed = false;

            $courseCode = $nonElectiveProgramCourse->courseCode;
            $courseType = $nonElectiveProgramCourse->courseType;
            $semester = $nonElectiveProgramCourse->semester;
            $level = $nonElectiveProgramCourse->level;

            $message = "{$courseCode} ({$courseType->value}) - {$level} Level {$semester} Semester";

            $model = ProgramCurriculumCourse::find($nonElectiveProgramCourse->programCourseId);

            $this->report($model, $message);
        }

        return $passed;
    }
}
