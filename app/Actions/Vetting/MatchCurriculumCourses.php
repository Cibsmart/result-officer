<?php

declare(strict_types=1);

namespace App\Actions\Vetting;

use App\Data\Query\StudentCoursesData;
use App\Enums\EntryMode;
use App\Enums\VettingStatus;
use App\Enums\VettingType;
use App\Models\ProgramCurriculum;
use App\Models\Registration;
use App\Models\Student;
use App\Queries\ProgramCourses;
use App\Queries\StudentCourses;

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

        $registeredCourseIds = $registrations->pluck('courseId');

        foreach ($programCourses as $programCourse) {
            $courseId = $programCourse->courseId;

            if ($registeredCourseIds->doesntContain($courseId)) {
                continue;
            }

            Registration::query()->whereIn('id', $registrations->pluck('registrationId')->all())
                ->where('course_id', $courseId)
                ->whereNull('program_curriculum_course_id')
                ->update(['program_curriculum_course_id' => $programCourse->programCourseId]);
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
}
