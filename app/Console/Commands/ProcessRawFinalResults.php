<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Enums\ImportEventStatus;
use App\Enums\Months;
use App\Enums\RawDataStatus;
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
use App\Pipelines\Checks\FinalResultImport\CheckCourseCode;
use App\Pipelines\Checks\FinalResultImport\CheckCreditUnit;
use App\Pipelines\Checks\FinalResultImport\CheckExam;
use App\Pipelines\Checks\FinalResultImport\CheckGrade;
use App\Pipelines\Checks\FinalResultImport\CheckInCourse;
use App\Pipelines\Checks\FinalResultImport\CheckMonth;
use App\Pipelines\Checks\FinalResultImport\CheckRegistrationNumber;
use App\Pipelines\Checks\FinalResultImport\CheckSemester;
use App\Pipelines\Checks\FinalResultImport\CheckSession;
use App\Pipelines\Checks\FinalResultImport\CheckTotal;
use App\Pipelines\Checks\FinalResultImport\CheckYear;
use App\Values\CourseCode;
use Illuminate\Console\Command;
use Illuminate\Pipeline\Pipeline;
use Illuminate\Support\Collection;

final class ProcessRawFinalResults extends Command
{
    protected $signature = 'rp:process-raw-final-results';

    protected $description = 'Process Raw Final Results and Store in Final Results';

    public function handle(): int
    {
        $importEvents = ExcelImportEvent::query()
            ->where('status', ImportEventStatus::UPLOADED)
            ->get();

        if ($importEvents->isEmpty()) {
            return Command::SUCCESS;
        }

        $importEvents->toQuery()->update(['status' => ImportEventStatus::PROCESSING->value]);

        foreach ($importEvents as $event) {
            assert($event instanceof ExcelImportEvent);

            $messages = collect($this->preprocess($event))->filter();

            if ($messages->isNotEmpty()) {
                $event->setMessage($this->joinMessages($messages));

                continue;
            }

            $this->processEventStudents($event);

            $event->updateStatus(ImportEventStatus::COMPLETED);
        }

        return Command::SUCCESS;
    }

    /** @return array<string, array<int, string>> */
    private function preprocess(ExcelImportEvent $event): array
    {
        return app(Pipeline::class)
            ->send(['event' => $event, 'errors' => []])
            ->through([
                CheckRegistrationNumber::class,
                CheckInCourse::class,
                CheckExam::class,
                CheckTotal::class,
                CheckGrade::class,
                CheckCreditUnit::class,
                CheckSemester::class,
                CheckSession::class,
                CheckCourseCode::class,
                CheckYear::class,
                CheckMonth::class,
            ])->thenReturn()['errors'];
    }

    /** @param \Illuminate\Support\Collection<string, non-empty-array<int, string>> $messages */
    private function joinMessages(Collection $messages): string
    {
        return $messages
            ->map(fn (array $value, string $key) => "{$key}: " . collect($value)->join(', '))
            ->join("\n");
    }

    private function processEventStudents(ExcelImportEvent $event): void
    {
        $registrationNumbers = $event->getUniqueRegistrationNumbers();

        foreach ($registrationNumbers as $registrationNumber) {
            $student = Student::getUsingRegistrationNumber($registrationNumber);

            $pendingResults = $event->getPendingRawFinalResultsFor($student->registration_number);

            if ($pendingResults->isEmpty()) {
                continue;
            }

            $this->checkAndUpdateOldRegistrationNumber($student, $pendingResults);

            $this->processFinalSessionResults($event, $pendingResults, $student);
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
        ExcelImportEvent $event,
        Collection $results,
        Student $student,
    ): void {
        $finalStudent = $this->getFinalStudent($results->firstOrFail(), $event, $student);

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
