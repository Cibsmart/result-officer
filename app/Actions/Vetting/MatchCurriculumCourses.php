<?php

declare(strict_types=1);

namespace App\Actions\Vetting;

use App\Data\Query\StudentCoursesData;
use App\Enums\EntryMode;
use App\Enums\VettingStatus;
use App\Enums\VettingType;
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
            $programCourseModel = ProgramCurriculumCourse::query()
                ->where('id', $programCourse->programCourseId)
                ->firstOrFail();

            $alternatives = $this->getCourseAlternatives($programCourseModel)->pluck('alternate_course_id');

            $courseIds = [$programCourseModel->course_id, ...$alternatives];

            $registrationIds = $registrations->whereIn('courseId', $courseIds)->pluck('registrationId');

            if ($registrationIds->isEmpty()) {
                continue;
            }

            Registration::query()
                ->whereIn('id', $registrationIds)
                ->whereNull('program_curriculum_course_id')
                ->update(['program_curriculum_course_id' => $programCourseModel->id]);
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

    /** @return \Illuminate\Database\Eloquent\Collection<int, \App\Models\CourseAlternative> */
    private function getCourseAlternatives(ProgramCurriculumCourse $programCourse): Collection
    {
        $alternatives = $programCourse->courseAlternatives;

        if ($alternatives->isNotEmpty()) {
            return $alternatives;
        }

        return CourseAlternative::query()
            ->whereNull('program_curriculum_course_id')
            ->where('original_course_id', $programCourse->course_id)
            ->get();
    }
}
