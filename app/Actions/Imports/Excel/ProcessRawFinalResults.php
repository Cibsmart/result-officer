<?php

declare(strict_types=1);

namespace App\Actions\Imports\Excel;

use App\Enums\Months;
use App\Enums\RawDataStatus;
use App\Enums\StudentStatus;
use App\Enums\Year;
use App\Models\ExamOfficer;
use App\Models\ExcelImportEvent;
use App\Models\FinalCourse;
use App\Models\FinalResult;
use App\Models\FinalSemesterEnrollment;
use App\Models\FinalSessionEnrollment;
use App\Models\FinalStudent;
use App\Models\Level;
use App\Models\RawFinalResult;
use App\Models\Semester;
use App\Models\Session;
use App\Models\Student;
use App\Values\CourseCode;
use Illuminate\Support\Collection;

final class ProcessRawFinalResults
{
    public static function new(): self
    {
        return new self();
    }

    public function execute(ExcelImportEvent $event): void
    {
        $registrationNumbers = $event->getUniqueRegistrationNumbers();

        foreach ($registrationNumbers as $registrationNumber) {
            $student = Student::getUsingRegistrationNumber($registrationNumber);

            $pendingResults = $event->getPendingRawFinalResultsFor($student->registration_number);

            if ($pendingResults->isEmpty()) {
                continue;
            }

            $finalStudent = $this->getFinalStudent($pendingResults->firstOrFail(), $event, $student);

            $this->checkAndUpdateOldRegistrationNumber($student, $pendingResults);

            $this->processFinalSessionResults($pendingResults, $student, $finalStudent);

            $finalStudent->updateCountSumAndAverages();

            $student->updateStatus(StudentStatus::CLEARED);
        }
    }

    private function getFinalStudent(
        RawFinalResult $result,
        ExcelImportEvent $event,
        Student $student,
    ): FinalStudent {
        $months = Months::fromName($result->month);
        assert($months instanceof Months);

        $data = [
            'exam_officer_id' => ExamOfficer::getOrCreate($result->exam_officer)->id,
            'month' => $months->value,
            'user_id' => $event->user_id,
            'year' => $result->year,
        ];

        $data = array_filter($data, fn ($value) => $value !== null);

        return FinalStudent::getOrCreate($student, $data);
    }

    /** @param \Illuminate\Support\Collection<int, \App\Models\RawFinalResult> $results */
    private function checkAndUpdateOldRegistrationNumber(Student $student, Collection $results): void
    {
        $oldRegistrationNumber = $results->pluck('old_registration_number')->unique()->filter()->first();

        if ($oldRegistrationNumber === null) {
            return;
        }

        $student->old_registration_number = $oldRegistrationNumber;
        $student->save();
    }

    /** @param \Illuminate\Support\Collection<int, \App\Models\RawFinalResult> $results */
    private function processFinalSessionResults(
        Collection $results,
        Student $student,
        FinalStudent $finalStudent,
    ): void {
        $sessionCount = 1;

        foreach ($results->groupBy('session') as $sessionName => $sessionResults) {

            $session = Session::getUsingName($sessionName);
            $level = Level::getUsingName((string) $sessionResults->max('level'));
            $year = Year::from($sessionCount);

            $sessionEnrollment = FinalSessionEnrollment::getOrCreate(
                student: $student,
                finalStudent: $finalStudent,
                session: $session,
                level: $level,
                year: $year,
            );

            $this->processSemesterResults($sessionEnrollment, $sessionResults, $student);

            $sessionEnrollment->updateCountSumAndAverages();

            $sessionCount += 1;
        }
    }

    /** @param \Illuminate\Support\Collection<int, \App\Models\RawFinalResult> $sessionResults */
    private function processSemesterResults(
        FinalSessionEnrollment $sessionEnrollment,
        Collection $sessionResults,
        Student $student,
    ): void {
        foreach ($sessionResults->groupBy('semester') as $semesterName => $semesterResults) {
            $semester = Semester::getUsingName($semesterName);

            $semesterEnrollment = FinalSemesterEnrollment::getOrCreate($sessionEnrollment, $semester);

            $this->processFinalResults($semesterEnrollment, $semesterResults, $student);

            $semesterEnrollment->updateSumsAndAverage();
        }
    }

    /** @param \Illuminate\Support\Collection<int, \App\Models\RawFinalResult> $semesterResults */
    private function processFinalResults(
        FinalSemesterEnrollment $semesterEnrollment,
        Collection $semesterResults,
        Student $student,
    ): void {
        foreach ($semesterResults as $result) {

            $course = FinalCourse::getOrCreateUsingCodeAndTitle(
                CourseCode::new($result->course_code)->value,
                $result->course_title,
            );

            if ($course->checkForDuplicateInFinalSemesterEnrollment($semesterEnrollment)) {
                $result->updateStatus(RawDataStatus::DUPLICATE);

                continue;
            }

            $status = $course->getCourseStatus($student);

            FinalResult::createFromRawFinalResult($semesterEnrollment, $result, $course, $status);

            $result->updateStatus(RawDataStatus::PROCESSED);
        }
    }
}
