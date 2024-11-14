<?php

declare(strict_types=1);

namespace App\Actions\Vetting;

use App\Enums\CourseType;
use App\Enums\CreditUnit;
use App\Enums\VettingStatus;
use App\Models\ProgramCurriculum;
use App\Models\ProgramCurriculumSemester;
use App\Models\Student;
use App\Models\VettingStep;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

use function assert;

final class VerifyElectiveCourses extends ReportVettingStep
{
    public function execute(Student $student, VettingStep $vettingStep): VettingStatus
    {
        /** @var \Illuminate\Database\Eloquent\Collection<int, \App\Models\Registration> $registrations */
        $registrations = $student->registrations()
            ->whereNotNull('program_curriculum_course_id')
            ->get();

        if ($registrations->isEmpty()) {
            $message = "Elective Courses Not Checked for {$student->registration_number} \n";

            $this->createReport($student, $vettingStep, $message);

            return VettingStatus::UNCHECKED;
        }

        $programCurriculum = $student->programCurriculum();

        if (is_null($programCurriculum)) {
            return VettingStatus::UNCHECKED;
        }

        $passed = $this->checkStudentElectiveCourses($programCurriculum, $registrations, $vettingStep);

        return $passed
            ? VettingStatus::PASSED
            : VettingStatus::FAILED;
    }

    /** @param \Illuminate\Database\Eloquent\Collection<int, \App\Models\Registration> $registrations */
    private function checkStudentElectiveCourses(
        ProgramCurriculum $programCurriculum,
        Collection $registrations,
        VettingStep $vettingStep,
    ): bool {

        $programCurriculumSemesters = $programCurriculum->programCurriculumSemesters()
            ->where(fn (Builder $query) => $query->where('minimum_elective_units', '>', 0)
                ->orWhere('minimum_elective_count', '>', 0))
            ->with('programCurriculumCourses')
            ->get();

        $passed = true;

        foreach ($programCurriculumSemesters as $programCurriculumSemester) {
            if ($this->passSemesterElectiveCoursesCheck($programCurriculumSemester, $registrations)) {
                continue;
            }

            $passed = false;

            $level = $programCurriculumSemester->programCurriculumLevel->level;
            $semester = $programCurriculumSemester->semester;

            $message = "Elective Course(s) for {$level->name} Level {$semester->name} Semester not complete \n";

            $this->createReport($programCurriculumSemester, $vettingStep, $message);
        }

        return $passed;
    }

    /** @param \Illuminate\Database\Eloquent\Collection<int, \App\Models\Registration> $registrations */
    private function passSemesterElectiveCoursesCheck(
        ProgramCurriculumSemester $programCurriculumSemester,
        Collection $registrations,
    ): bool {

        $minElectiveUnits = $programCurriculumSemester->minimum_elective_units;
        $minElectiveCount = $programCurriculumSemester->minimum_elective_count;

        $curriculumSemesterCourses = $programCurriculumSemester
            ->programCurriculumCourses()
            ->where('course_type', CourseType::ELECTIVE)
            ->get();

        [$numberOfElectives, $sumOfCreditUnits] = [0, 0];

        foreach ($curriculumSemesterCourses as $curriculumSemesterCourse) {

            $registration = $registrations->firstWhere('program_curriculum_course_id', $curriculumSemesterCourse->id);

            if ($registration === null) {
                continue;
            }

            $creditUnit = $curriculumSemesterCourse->credit_unit;
            assert($creditUnit instanceof CreditUnit);

            $numberOfElectives += 1;
            $sumOfCreditUnits += $creditUnit->value;
        }

        return $numberOfElectives >= $minElectiveCount
            && $sumOfCreditUnits >= $minElectiveUnits->value;
    }
}
