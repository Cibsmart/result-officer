<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Enums\CourseStatus;
use App\Enums\RawDataStatus;
use App\Enums\StudentStatus;
use App\Enums\Year;
use App\Models\Course;
use App\Models\LegacyResult;
use App\Models\LegacyStudent;
use App\Models\Level;
use App\Models\Registration;
use App\Models\Result;
use App\Models\Semester;
use App\Models\SemesterEnrollment;
use App\Models\Session;
use App\Models\SessionEnrollment;
use App\Models\Student;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Collection;

final class ProcessLegacyResults extends Command
{
    protected $signature = 'rp:process-legacy-results';

    protected $description = 'Process legacy results';

    public function handle(): void
    {
        $pendingStudents = LegacyResult::query()
            ->where('status', RawDataStatus::PENDING)
            ->distinct()
            ->pluck('student_id');

        $legacyStudents = LegacyStudent::query()
            ->where('status', StudentStatus::NEW)
            ->where('process_status', RawDataStatus::PROCESSED)
            ->whereIn('legacy_id', $pendingStudents)
            ->lazyById();

        $bar = $this->output->createProgressBar(count($legacyStudents));

        $bar->start();

        foreach ($legacyStudents as $legacyStudent) {

            $results = LegacyResult::query()
                ->where('student_id', $legacyStudent->legacy_id)
                ->where('status', RawDataStatus::PENDING)
                ->orderBy('session')
                ->orderBy('semester')
                ->orderBy('course_code')
                ->orderBy('exam', 'desc')
                ->orderBy('inc', 'desc')
                ->get();

            if ($results->isEmpty()) {
                $bar->advance();

                continue;
            }

            $this->processLegacyStudentResults($legacyStudent, $results);

            $bar->advance();
        }

        $bar->finish();
    }

    /** @param \Illuminate\Database\Eloquent\Collection<int, \App\Models\LegacyResult> $results */
    private function processLegacyStudentResults(
        LegacyStudent $legacyStudent,
        Collection $results,
    ): void {
        $student = Student::findOrFail($legacyStudent->student_id);

        $courses = [];

        $sessionEnrollments = $results->groupBy('session');

        $sessionCount = 1;

        foreach ($sessionEnrollments as $sessionName => $sessionRegistrations) {

            $session = Session::getUsingName($sessionName);
            $level = Level::getUsingName((string) $sessionRegistrations->flatten()->max('level'));
            $year = Year::from($sessionCount);

            $dbSessionEnrollment = SessionEnrollment::getOrCreate($student, $session, $level, $year);

            $this->processSessionEnrollments($dbSessionEnrollment, $sessionRegistrations, $courses);

            $sessionCount += 1;
        }
    }

    /**
     * @param \Illuminate\Database\Eloquent\Collection<int, \App\Models\LegacyResult> $sessionRegistrations
     * @param array<string, int> $courses
     */
    private function processSessionEnrollments(
        SessionEnrollment $dbSessionEnrollment,
        Collection $sessionRegistrations,
        array $courses,
    ): void {
        $semesterEnrollments = $sessionRegistrations->groupBy('semester');

        foreach ($semesterEnrollments as $semesterName => $semesterRegistrations) {
            $semester = Semester::getUsingName($semesterName);
            $dbSemesterEnrollment = SemesterEnrollment::getOrCreate($dbSessionEnrollment, $semester);

            $this->processSemesterEnrollments($dbSemesterEnrollment, $semesterRegistrations, $courses);
        }
    }

    /**
     * @param \Illuminate\Database\Eloquent\Collection<int, \App\Models\LegacyResult> $semesterRegistrations
     * @param array<string, int> $courses
     */
    private function processSemesterEnrollments(
        SemesterEnrollment $dbSemesterEnrollment,
        Collection $semesterRegistrations,
        array $courses,
    ): void {
        foreach ($semesterRegistrations as $legacyResult) {
            try {
                $course = Course::getUsingLegacyCourseId($legacyResult->legacy_course_id);

                $status = CourseStatus::REPEAT;

                if (! array_key_exists($course->code, $courses)) {
                    $status = CourseStatus::FRESH;
                    $courses[$course->code] = $course->id;
                }

                $dbRegistration = Registration::createFromLegacyResult($dbSemesterEnrollment, $legacyResult, $status);

                Result::createFromLegacyResult($dbRegistration, $legacyResult);

                $legacyResult->updateSuccess($dbRegistration);
            } catch (Exception $e) {
                $legacyResult->updateFailure($e->getMessage());
            }
        }
    }
}
