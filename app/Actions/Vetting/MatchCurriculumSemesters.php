<?php

declare(strict_types=1);

namespace App\Actions\Vetting;

use App\Enums\EntryMode;
use App\Enums\VettingStatus;
use App\Enums\VettingType;
use App\Models\ProgramCurriculum;
use App\Models\ProgramCurriculumLevel;
use App\Models\SessionEnrollment;
use App\Models\Student;

final class MatchCurriculumSemesters extends ReportVettingStep
{
    public function execute(Student $student): VettingStatus
    {
        $this->createVettingStep($student, VettingType::MATCH_SEMESTERS);

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
        $programLevels = $curriculum->programCurriculumLevels;

        $sessionEnrollments = $student->sessionEnrollments->sortBy('year')->values();

        foreach ($programLevels as $programLevel) {
            $sessionEnrollment = $sessionEnrollments->firstWhere('level_id', $programLevel->level_id);

            if (is_null($sessionEnrollment)) {
                continue;
            }

            $this->updateSemesterEnrollmentsWithProgramSemesters($sessionEnrollment, $programLevel);
        }
    }

    private function checkAndReportUnMatchedCourses(Student $student): bool
    {
        $semesterEnrollments = $student->semesterEnrollments()
            ->with('sessionEnrollment.session', 'semester')
            ->whereNull('program_curriculum_semester_id')
            ->get();

        if ($semesterEnrollments->isEmpty()) {
            return true;
        }

        foreach ($semesterEnrollments as $semesterEnrollment) {

            $semester = $semesterEnrollment->semester;
            $session = $semesterEnrollment->sessionEnrollment->session;

            $message = "{$session->name} session - {$semester->name} semester";

            $this->report($semesterEnrollment, $message);
        }

        return false;
    }

    private function updateSemesterEnrollmentsWithProgramSemesters(
        SessionEnrollment $sessionEnrollment,
        ProgramCurriculumLevel $programLevel,
    ): void {
        foreach ($programLevel->programCurriculumSemesters as $programSemester) {

            $semesterEnrollment = $sessionEnrollment->semesterEnrollments
                ->firstWhere('semester_id', $programSemester->semester_id);

            if (is_null($semesterEnrollment)) {
                continue;
            }

            $semesterEnrollment->update(['program_curriculum_semester_id' => $programSemester->id]);
        }
    }
}
