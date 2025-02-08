<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Enums\CourseStatus;
use App\Enums\CreditUnit;
use App\Enums\Grade;
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
use App\Values\CourseCode;
use App\Values\ExamScore;
use App\Values\InCourseScore;
use App\Values\RegistrationNumber;
use App\Values\SessionValue;
use App\Values\TotalScore;
use Exception;
use Illuminate\Console\Command;

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
            $messages = collect($this->preprocess($event))->filter();

            if ($messages->isNotEmpty()) {
                $message = $messages
                    ->map(fn (array $value, string $key) => "{$key}: " . collect($value)->join(', '))
                    ->join("\n");

                $event->setMessage($message);

                return Command::FAILURE;
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

                //Handle Old Registration Number

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
        $messages = [];

        $messages = [...$messages, ...$this->checkRegistrationNumbers($event)];
        $messages = [...$messages, ...$this->checkInCourse($event)];
        $messages = [...$messages, ...$this->checkExam($event)];
        $messages = [...$messages, ...$this->checkTotal($event)];
        $messages = [...$messages, ...$this->checkGrade($event)];
        $messages = [...$messages, ...$this->checkCreditUnit($event)];
        $messages = [...$messages, ...$this->checkSemester($event)];
        $messages = [...$messages, ...$this->checkSession($event)];
        $messages = [...$messages, ...$this->checkCourseCode($event)];
        $messages = [...$messages, ...$this->checkYear($event)];

        return [...$messages, ...$this->checkMonth($event)];
    }

    /** @return array<string, array<int, string>> */
    private function checkRegistrationNumbers(ExcelImportEvent $event): array
    {
        $messages = [];

        $registrationNumbers = $event->rawFinalResults()->pluck('registration_number')->unique();

        $dbRegistrationNumbers = Student::query()
            ->whereIn('registration_number', $registrationNumbers)
            ->pluck('registration_number');

        foreach ($registrationNumbers as $registrationNumber) {
            try {
                RegistrationNumber::new($registrationNumber);
            } catch (Exception) {
                $messages[] = $registrationNumber;
            }
        }

        $diff = $registrationNumbers->diff($dbRegistrationNumbers);

        return ['invalid_registration_number' => $messages, 'student_record_not_found' => $diff->toArray()];
    }

    /** @return array<string, array<int, string>> */
    private function checkInCourse(ExcelImportEvent $event): array
    {
        $messages = [];

        $inCourses = $event->rawFinalResults()->pluck('in_course')->unique();

        foreach ($inCourses as $inCourse) {
            try {
                InCourseScore::new($inCourse);
            } catch (Exception) {
                $messages[] = $inCourse;
            }
        }

        return ['invalid_incourse_score' => $messages];
    }

    /** @return array<string, array<int, string>> */
    private function checkExam(ExcelImportEvent $event): array
    {
        $messages = [];

        $exams = $event->rawFinalResults()->pluck('exam')->unique();

        foreach ($exams as $exam) {
            try {
                ExamScore::new($exam);
            } catch (Exception) {
                $messages[] = $exam;
            }
        }

        return ['invalid_exam_score' => $messages];
    }

    /** @return array<string, array<int, string>> */
    private function checkTotal(ExcelImportEvent $event): array
    {
        $messages = [];

        $totals = $event->rawFinalResults()->pluck('total')->unique();

        foreach ($totals as $total) {
            try {
                TotalScore::new($total);
            } catch (Exception) {
                $messages[] = $total;
            }
        }

        return ['invalid_total_score' => $messages];
    }

    /** @return array<string, array<int, string>> */
    private function checkGrade(ExcelImportEvent $event): array
    {
        $messages = [];

        $grades = $event->rawFinalResults()->pluck('grade')->unique();

        foreach ($grades as $grade) {
            if (Grade::tryFrom($grade) !== null) {
                continue;
            }

            $messages[] = $grade;
        }

        return ['invalid_grade' => $messages];
    }

    /** @return array<string, array<int, string>> */
    private function checkCreditUnit(ExcelImportEvent $event): array
    {
        $messages = [];

        $creditUnits = $event->rawFinalResults()->pluck('credit_unit')->unique();

        foreach ($creditUnits as $creditUnit) {
            if (CreditUnit::tryFrom($creditUnit) !== null) {
                continue;
            }

            $messages[] = $creditUnit;
        }

        return ['invalid_credit_unit' => $messages];
    }

    /** @return array<string, array<int, string>> */
    private function checkSemester(ExcelImportEvent $event): array
    {
        $messages = [];

        $semesters = $event->rawFinalResults()->pluck('semester')->unique();

        foreach ($semesters as $semester) {
            if (in_array(strtolower($semester), ['first', 'second'], true)) {
                continue;
            }

            $messages[] = $semester;
        }

        return ['invalid_semester' => $messages];
    }

    /** @return array<string, array<int, string>> */
    private function checkSession(ExcelImportEvent $event): array
    {
        $messages = [];

        $sessions = $event->rawFinalResults()->pluck('session')->unique();

        foreach ($sessions as $session) {
            try {
                SessionValue::new($session);
            } catch (Exception) {
                $messages[] = $session;
            }
        }

        return ['invalid_session' => $messages];
    }

    /** @return array<string, array<int, string>> */
    private function checkCourseCode(ExcelImportEvent $event): array
    {
        $messages = [];

        $courseCodes = $event->rawFinalResults()->pluck('course_code')->unique();

        foreach ($courseCodes as $courseCode) {
            try {
                CourseCode::new($courseCode);
            } catch (Exception) {
                $messages[] = $courseCode;
            }
        }

        return ['invalid_course_code' => $messages];
    }

    /** @return array<string, array<int, string>> */
    private function checkYear(ExcelImportEvent $event): array
    {
        $messages = [];

        $years = $event->rawFinalResults()->pluck('year')->unique();

        foreach ($years as $year) {
            if (preg_match('/^20\d{2}$/i', $year) && $year <= now()->addYear()->year) {
                continue;
            }

            $messages[] = $year;
        }

        return ['invalid_year' => $messages];
    }

    /** @return array<string, array<int, string>> */
    private function checkMonth(ExcelImportEvent $event): array
    {
        $messages = [];

        $months = $event->rawFinalResults()->pluck('month')->unique();

        foreach ($months as $month) {
            if (Months::fromName($month) !== null) {
                continue;
            }

            $messages[] = $month;
        }

        return ['invalid_month' => $messages];
    }
}
