<?php

declare(strict_types=1);

namespace App\Actions\Vetting;

use App\Data\Query\StudentCoursesData;
use App\Enums\CourseType;
use App\Enums\CreditUnit;
use App\Enums\VettingStatus;
use App\Enums\VettingType;
use App\Models\ProgramCurriculum;
use App\Models\ProgramCurriculumSemester;
use App\Models\Student;
use App\Queries\StudentCourses;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

use function assert;

final class VerifyElectiveCourses extends ReportVettingStep
{
    public function execute(Student $student): VettingStatus
    {
        $this->createVettingStep($student, VettingType::CHECK_ELECTIVE_COURSES);

        /** @var \Illuminate\Database\Eloquent\Collection<int, \App\Models\Registration> $registrations */
        $registrations = StudentCourses::for($student)->query()->whereNotNull('program_curriculum_course_id')->get();
        $registrations = StudentCoursesData::collect($registrations);

        if ($registrations->isEmpty()) {
            $message = "Elective Courses Not Checked for {$student->registration_number} \n";

            $this->report($student, $message);

            return VettingStatus::UNCHECKED;
        }

        $programCurriculum = $student->programCurriculum();

        if (is_null($programCurriculum)) {
            return VettingStatus::UNCHECKED;
        }

        $passed = $this->checkStudentElectiveCourses($programCurriculum, $registrations);

        return $passed
            ? VettingStatus::PASSED
            : VettingStatus::FAILED;
    }

    /** @param \Illuminate\Support\Collection<int, \App\Data\Query\StudentCoursesData> $registrations */
    private function checkStudentElectiveCourses(
        ProgramCurriculum $programCurriculum,
        Collection $registrations,
    ): bool {

        $programSemesters = $programCurriculum->programCurriculumSemesters()
            ->where(fn (Builder $query) => $query->where('minimum_elective_units', '>', 0)
                ->orWhere('minimum_elective_count', '>', 0))
            ->with('semester', 'programCurriculumCourses', 'programCurriculumLevel.level',
                'programCurriculumElectiveGroups.programCurriculumElectiveCourses')
            ->get();

        $passed = true;

        foreach ($programSemesters as $programSemester) {
            $passElectiveCountUnitCheck = $this->semesterElectiveCountUnitCheck($programSemester, $registrations);
            $passElectiveGroupCheck = $this->semesterElectiveGroupCheck($programSemester, $registrations);

            if ($passElectiveCountUnitCheck && $passElectiveGroupCheck) {
                continue;
            }

            $passed = false;
        }

        return $passed;
    }

    /** @param \Illuminate\Support\Collection<int, \App\Data\Query\StudentCoursesData> $registrations */
    private function semesterElectiveCountUnitCheck(
        ProgramCurriculumSemester $programSemester,
        Collection $registrations,
    ): bool {

        $programCourses = $programSemester
            ->programCurriculumCourses()
            ->where('course_type', CourseType::ELECTIVE)
            ->get();

        [$numberOfElectives, $sumOfCreditUnits] = [0, 0];

        foreach ($programCourses as $programCourse) {

            $registration = $registrations->firstWhere('programCurriculumCourseId', $programCourse->id);

            if ($registration === null) {
                continue;
            }

            $creditUnit = $programCourse->credit_unit;
            assert($creditUnit instanceof CreditUnit);

            $numberOfElectives += 1;
            $sumOfCreditUnits += $creditUnit->value;
        }

        return $this->checkAndReportCountUnitDeficiency($programSemester, $numberOfElectives, $sumOfCreditUnits);
    }

    /** @param \Illuminate\Support\Collection<int, \App\Data\Query\StudentCoursesData> $registrations */
    private function semesterElectiveGroupCheck(
        ProgramCurriculumSemester $programSemester,
        Collection $registrations,
    ): bool {
        $semesterElectiveGroups = $programSemester->programCurriculumElectiveGroups;

        if ($semesterElectiveGroups->isEmpty()) {
            return true;
        }

        $registrationProgramCourseIds = $registrations->pluck('programCurriculumCourseId');

        $passed = true;

        foreach ($semesterElectiveGroups as $semesterElectiveGroup) {
            $electiveGroupCourseIds = $semesterElectiveGroup->programCurriculumElectiveCourses->pluck('id');

            //Empty diff: all courses taken; equals group total: none taken; otherwise, some taken.
            $diff = $electiveGroupCourseIds->diff($registrationProgramCourseIds);

            if ($diff->isEmpty() || $electiveGroupCourseIds->count() === $diff->count()) {
                continue;
            }

            $passed = false;

            $level = $programSemester->programCurriculumLevel->level;
            $semester = $programSemester->semester;

            $message = "Did not take all courses in elective group ({$semesterElectiveGroup->name}) for ";
            $message .= "{$level->name} Level {$semester->name} Semester\n";

            $this->report($semesterElectiveGroup, $message);
        }

        return $passed;
    }

    private function checkAndReportCountUnitDeficiency(
        ProgramCurriculumSemester $programSemester,
        int $numberOfElectives,
        int $sumOfCreditUnits,
    ): bool {
        $minElectiveUnits = $programSemester->minimum_elective_units;
        $minElectiveCount = $programSemester->minimum_elective_count;

        $electiveCountCheck = $numberOfElectives >= $minElectiveCount;
        $electiveUnitCheck = $sumOfCreditUnits >= $minElectiveUnits->value;

        if (! $electiveCountCheck) {
            $this->reportFailure($programSemester, 'count');
        }

        if (! $electiveUnitCheck) {
            $this->reportFailure($programSemester, 'unit');
        }

        return $electiveCountCheck && $electiveUnitCheck;
    }

    private function reportFailure(ProgramCurriculumSemester $programSemester, string $type): void
    {
        $level = $programSemester->programCurriculumLevel->level;
        $semester = $programSemester->semester;

        $message = "Insufficient elective course {$type} for {$level->name} Level {$semester->name} Semester\n";

        $this->report($programSemester, $message);
    }
}
