<?php

declare(strict_types=1);

namespace App\Actions\Vetting;

use App\Data\Query\StudentCoursesData;
use App\Enums\EntryMode;
use App\Enums\VettingStatus;
use App\Enums\VettingType;
use App\Models\Course;
use App\Models\CourseAlternative;
use App\Models\ProgramCurriculum;
use App\Models\ProgramCurriculumCourse;
use App\Models\Registration;
use App\Models\Student;
use App\Queries\ProgramCourses;
use App\Queries\StudentCourses;
use Illuminate\Database\Eloquent\Collection;

final class MatchCurriculumCourses extends ReportVettingStep
{
    public function execute(Student $student): VettingStatus
    {
        $this->createVettingStep($student, VettingType::MATCH_COURSES);

        $curriculum = $student->programCurriculum();

        if (is_null($curriculum)) {
            $program = $student->program;
            $entryMode = $student->entry_mode;
            $entrySession = $student->entrySession;

            assert($entryMode instanceof EntryMode);

            $message = "{$program->name} - {$entrySession->name} - ({$entryMode->value})";

            $this->report($program, $message);

            return VettingStatus::UNCHECKED;
        }

        $this->updateProgramCurriculumId($curriculum, $student);

        $passed = $this->checkAndReportUnMatchedCourses($student);

        return $passed
            ? VettingStatus::PASSED
            : VettingStatus::FAILED;
    }

    private function updateProgramCurriculumId(ProgramCurriculum $curriculum, Student $student): void
    {
        $programCourses = ProgramCourses::for($curriculum)->get();

        $registrations = StudentCourses::for($student)->query()
            ->whereNull('program_curriculum_course_id')
            ->get();

        $registrations = StudentCoursesData::collect($registrations);

        foreach ($programCourses as $programCourse) {
            $programCourseModel = ProgramCurriculumCourse::getUsingId($programCourse->programCourseId);

            $alternativeCourses = $this->getAlternativeCourses($programCourseModel);

            $courseIds = [$programCourseModel->course_id, ...$alternativeCourses->pluck('id')];
            $courseCodes = [$programCourseModel->course->code, ...$alternativeCourses->pluck('code')];

            $courseIdMatches = $registrations->whereIn('courseId', $courseIds)->pluck('registrationId');

            $courseCodeMatches = $registrations->whereIn('courseCode', $courseCodes)->pluck('registrationId');

            $registrationIds = $courseIdMatches->merge($courseCodeMatches)->unique();

            if ($registrationIds->isEmpty()) {
                continue;
            }

            Registration::updateCurriculumCourseId($registrationIds, $programCourseModel);
        }
    }

    private function checkAndReportUnMatchedCourses(Student $student): bool
    {
        $registrations = StudentCourses::for($student)->query()
            ->whereNull('program_curriculum_course_id')
            ->get();

        $registrations = StudentCoursesData::collect($registrations);

        if ($registrations->isEmpty()) {
            return true;
        }

        foreach ($registrations as $registration) {

            $message = "{$registration->courseCode} - {$registration->session} {$registration->semester} semester";

            $model = Registration::find($registration->registrationId);

            $this->report($model, $message);
        }

        return false;
    }

    /** @return \Illuminate\Database\Eloquent\Collection<int, \App\Models\Course> */
    private function getAlternativeCourses(ProgramCurriculumCourse $programCourse): Collection
    {
        $alternativeIds = $programCourse->courseAlternatives->pluck('alternate_course_id');

        if ($alternativeIds->isNotEmpty()) {
            return Course::query()->whereIn('id', $alternativeIds)->get();
        }

        $alternativeIds = CourseAlternative::getUsingOriginalCourseId($programCourse->course_id)
            ->pluck('alternate_course_id');

        return Course::query()->whereIn('id', $alternativeIds)->get();
    }
}
