<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Enums\CourseStatus;
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

//        $importEvents->toQuery()->update(['status' => ImportEventStatus::PROCESSING->value]);

        foreach ($importEvents as $event) {
            $messages = collect($this->preprocess($event))->filter();

            if ($messages->isNotEmpty()) {
                $message = $messages
                    ->map(fn (array $value, string $key) => "{$key}: " . collect($value)->join(', '))
                    ->join("\n");

                $event->setMessage($message);

                continue;
            }

            $registrationNumbers = $event->rawFinalResults()
                ->orderBy('registration_number')
                ->pluck('registration_number')
                ->unique();

            foreach ($registrationNumbers as $registrationNumber) {
                $student = Student::query()->where('registration_number', $registrationNumber)->firstOrFail();

                $courses = [];

                $pendingResults = $event->rawFinalResults()
                    ->where('status', 'pending')
                    ->where('registration_number', $registrationNumber)
                    ->orderBy('session')
                    ->orderBy('semester')
                    ->orderBy('course_code')
                    ->get();

                if ($pendingResults->isEmpty()) {
                    continue;
                }

                $oldRegistrationNumber = $pendingResults->pluck('old_registration_number')->unique()->filter()->first();

                if ($oldRegistrationNumber !== null) {
                    $student->old_registration_number = $oldRegistrationNumber;
                    $student->save();
                }

                $first = $pendingResults->first();
                assert($first !== null);

                $data = [
                    'exam_officer_id' => ExamOfficer::getOrCreate($first['exam_officer'])->id,
                    'month' => Months::fromName($first['month']),
                    'user_id' => $event->user_id,
                    'year' => $first['year'],
                ];

                $finalStudent = FinalStudent::getOrCreate($student, $data);
                $sessionCount = 1;

                foreach ($pendingResults->groupBy('session') as $sessionName => $sessionResults) {

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

                    foreach ($sessionResults->groupBy('semester') as $semesterName => $semesterResults) {
                        $semester = Semester::getUsingName($semesterName);

                        $semesterEnrollment = FinalSemesterEnrollment::getOrCreate($sessionEnrollment, $semester);

                        foreach ($semesterResults as $result) {

                            $course = FinalCourse::getOrCreateUsingCodeAndTitle(
                                CourseCode::new($result->course_code)->value,
                                $result->course_title,
                            );

                            if ($course->checkForDuplicateInFinalSemesterEnrollment($semesterEnrollment)) {
                                $result->updateStatus(RawDataStatus::DUPLICATE);

                                continue;
                            }

                            $status = CourseStatus::REPEAT;

                            if (! array_key_exists($course->code, $courses)) {
                                $status = CourseStatus::FRESH;
                                $courses[$course->code] = $course->id;
                            }

                            FinalResult::createFromRawFinalResult($semesterEnrollment, $result, $course, $status);

                            $result->updateStatus(RawDataStatus::PROCESSED);
                        }
                    }

                    $sessionCount += 1;
                }
            }

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
}
