<?php

declare(strict_types=1);

namespace App\Actions\Imports\Excel;

use App\Enums\RawDataStatus;
use App\Enums\StudentStatus;
use App\Enums\Year;
use App\Models\Course;
use App\Models\ExcelImportEvent;
use App\Models\Level;
use App\Models\RawExcelResult;
use App\Models\Registration;
use App\Models\Result;
use App\Models\Semester;
use App\Models\SemesterEnrollment;
use App\Models\Session;
use App\Models\SessionEnrollment;
use App\Models\Student;
use App\Values\CourseCode;
use App\Values\CourseTitle;
use Illuminate\Support\Collection;

final class ProcessRawExcelResults
{
    public static function new(): self
    {
        return new self();
    }

    public function execute(ExcelImportEvent $event): void
    {
        $registrationNumbers = RawExcelResult::getUniqueRegistrationNumbers($event);

        foreach ($registrationNumbers as $registrationNumber) {
            $student = Student::getUsingRegistrationNumber($registrationNumber);

            $pendingResults = RawExcelResult::getPendingRawResults($event, $student->registration_number);

            if ($pendingResults->isEmpty()) {
                continue;
            }

            $this->processExcelSessionResults($pendingResults, $student);

            $student->updateStatus(StudentStatus::CLEARED);
        }
    }

    /** @param \Illuminate\Support\Collection<int, \App\Models\RawExcelResult> $results */
    private function processExcelSessionResults(Collection $results, Student $student): void
    {
        $sessionCount = 1;

        foreach ($results->groupBy('session') as $sessionName => $sessionResults) {

            $session = Session::getUsingName($sessionName);
            $level = Level::getUsingName((string) $sessionResults->max('level'));
            $year = Year::from($sessionCount);

            $sessionEnrollment = SessionEnrollment::getOrCreate(
                student: $student,
                session: $session,
                level: $level,
                year: $year,
            );

            $this->processSemesterResults($sessionEnrollment, $sessionResults);

            $sessionCount += 1;
        }
    }

    /** @param \Illuminate\Support\Collection<int, \App\Models\RawExcelResult> $sessionResults */
    private function processSemesterResults(
        SessionEnrollment $sessionEnrollment,
        Collection $sessionResults,
    ): void {
        foreach ($sessionResults->groupBy('semester') as $semesterName => $semesterResults) {
            $semester = Semester::getUsingName($semesterName);

            $semesterEnrollment = SemesterEnrollment::getOrCreate($sessionEnrollment, $semester);

            $this->processFinalResults($semesterEnrollment, $semesterResults);
        }
    }

    /** @param \Illuminate\Support\Collection<int, \App\Models\RawExcelResult> $semesterResults */
    private function processFinalResults(
        SemesterEnrollment $semesterEnrollment,
        Collection $semesterResults,
    ): void {
        foreach ($semesterResults as $rawResult) {

            $course = Course::getOrCreateUsingCodeAndTitle(
                CourseCode::new($rawResult->course_code),
                CourseTitle::new($rawResult->course_title),
            );

            if ($course->checkForDuplicateInSemesterEnrollment($semesterEnrollment)) {
                $rawResult->updateStatus(RawDataStatus::DUPLICATE);

                continue;
            }

            $registration = Registration::createFromRawExcelResult(
                semesterEnrollment: $semesterEnrollment,
                rawResult: $rawResult,
                course: $course,
            );

            $result = Result::createFromRawExcelResult($registration, $rawResult);

            $rawResult->setRegistrationAndResult($registration, $result);

            $rawResult->updateStatus(RawDataStatus::PROCESSED);
        }
    }
}
