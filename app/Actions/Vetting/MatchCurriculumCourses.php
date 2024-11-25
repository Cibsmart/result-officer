<?php

declare(strict_types=1);

namespace App\Actions\Vetting;

use App\Enums\EntryMode;
use App\Enums\VettingStatus;
use App\Enums\VettingType;
use App\Models\ProgramCurriculum;
use App\Models\Registration;
use App\Models\Student;

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
        /** @var \Illuminate\Support\Collection<int, \App\Models\ProgramCurriculumCourse> $programCurriculumCourses */
        $programCurriculumCourses = $curriculum->programCurriculumCourses()->with('course')->get();
        $registrations = $student->registrations()->whereNull('program_curriculum_course_id')->get();

        foreach ($programCurriculumCourses as $programCurriculumCourse) {
            $course = $programCurriculumCourse->course;

            if ($registrations->where('course_id', $course->id)->isEmpty()) {
                continue;
            }

            $registrations->where('course_id', $course->id)->toQuery()
                ->update(['program_curriculum_course_id' => $programCurriculumCourse->id]);
        }
    }

    private function checkAndReportUnMatchedCourses(Student $student): bool
    {
        $registrations = $student->registrations()
            ->with('course', 'semesterEnrollment.semester', 'semesterEnrollment.sessionEnrollment.session')
            ->whereNull('program_curriculum_course_id')
            ->get();

        if ($registrations->isEmpty()) {
            return true;
        }

        foreach ($registrations as $registration) {

            assert($registration instanceof Registration);

            $code = $registration->course->code;
            $session = $registration->semesterEnrollment->sessionEnrollment->session;
            $semester = $registration->semesterEnrollment->semester;

            $message = "{$code} - {$session->name} {$semester->name} semester";

            $this->report($registration, $message);
        }

        return false;
    }
}
