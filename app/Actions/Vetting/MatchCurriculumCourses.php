<?php

declare(strict_types=1);

namespace App\Actions\Vetting;

use App\Enums\EntryMode;
use App\Enums\VettingStatus;
use App\Models\ProgramCurriculum;
use App\Models\Registration;
use App\Models\Student;
use App\Models\VettingStep;

final class MatchCurriculumCourses extends ReportVettingStep
{
    public function execute(Student $student, VettingStep $vettingStep): VettingStatus
    {
        $curriculum = $student->programCurriculum();

        if (is_null($curriculum)) {
            $program = $student->program;
            $entryMode = $student->entry_mode;
            $entrySession = $student->entrySession;

            assert($entryMode instanceof EntryMode);

            $message = "Curriculum not found for {$program->name} {$entrySession->name} ({$entryMode->value})  \n";

            $this->createReport($program, $vettingStep, $message);

            return VettingStatus::UNCHECKED;
        }

        $this->updateProgramCurriculumId($curriculum, $student);

        $passed = $this->checkAndReportUnMatchedCourses($student, $vettingStep);

        return $passed
            ? VettingStatus::PASSED
            : VettingStatus::FAILED;
    }

    private function updateProgramCurriculumId(ProgramCurriculum $curriculum, Student $student): void
    {
        $programCurriculumCourses = $curriculum->programCurriculumCourses;
        $registrations = $student->registrations()->whereNull('program_curriculum_course_id')->get();

        foreach ($programCurriculumCourses as $programCurriculumCourse) {
            $course = $programCurriculumCourse->course;

            $registrations->where('course_id', $course->id)->toQuery()
                ->update(['program_curriculum_course_id' => $programCurriculumCourse->id]);
        }
    }

    private function checkAndReportUnMatchedCourses(Student $student, VettingStep $vettingStep): bool
    {
        $registrations = $student->registrations()
            ->with('semesterEnrollment.semester', 'semesterEnrollment.sessionEnrollment.session')
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

            $message = "{$code} in {$session->name} {$semester->name}";
            $message .= " Semester does not match any course in the curriculum \n";

            $this->createReport($registration, $vettingStep, $message);
        }

        return false;
    }
}
