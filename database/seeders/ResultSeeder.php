<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\CourseStatus;
use App\Enums\CreditUnit;
use App\Enums\Grade;
use App\Helpers\CSVFile;
use App\Models\Course;
use App\Models\Level;
use App\Models\Registration;
use App\Models\Result;
use App\Models\Semester;
use App\Models\SemesterEnrollment;
use App\Models\Session;
use App\Models\SessionEnrollment;
use App\Models\Student;
use App\Values\ExamScore;
use App\Values\InCourseScore;
use App\Values\TotalScore;
use Illuminate\Database\Seeder;
use Illuminate\Support\Collection;

final class ResultSeeder extends Seeder
{
    public function run(): void
    {
        /** @var \Illuminate\Support\Collection<int, \Illuminate\Support\Collection<string, string>> $content */
        $content = (new CSVFile('seeders/results.csv'))->read();

        $students = $content->sortBy([
            ['registration_number', 'asc'],
            ['session', 'asc'],
            ['semester', 'asc'],
            ['level', 'desc'],
            ['course_code', 'asc'],
        ])->groupBy('registration_number');

        foreach ($students as $registrationNumber => $results) {
            $student = Student::query()->where('registration_number', $registrationNumber)->firstOrFail();

            $this->createSessionAndSemesterEnrollments($student, $results);
        }
    }

    /** @param \Illuminate\Support\Collection<int, \Illuminate\Support\Collection<string, string>> $results */
    private function createSessionAndSemesterEnrollments(
        Student $student,
        Collection $results,
    ): void {
        foreach ($results->groupBy('session') as $sessionName => $sessionResults) {
            $level = Level::getUsingName($sessionResults->first()['level']);
            $session = Session::getUsingName($sessionName);

            $sessionEnrollment = SessionEnrollment::getOrCreate($student, $session, $level);

            $student->updateStatus($student->getStatus());

            foreach ($sessionResults->groupBy('semester') as $semesterName => $semesterResults) {
                $semester = Semester::getUsingName($semesterName);

                $semesterEnrollment = SemesterEnrollment::getOrCreate($sessionEnrollment, $semester);

                $this->createRegistrationsAndResults($semesterEnrollment, $semesterResults);
            }
        }
    }

    /** @param \Illuminate\Support\Collection<int, \Illuminate\Support\Collection<string, string>> $semesterResults */
    private function createRegistrationsAndResults(
        SemesterEnrollment $semesterEnrollment,
        Collection $semesterResults,
    ): void {
        foreach ($semesterResults as $semesterResult) {
            $course = Course::getUsingCode($semesterResult['course_code']);

            $registration = Registration::query()->create([
                'course_id' => $course->id,
                'course_status' => CourseStatus::from($semesterResult['course_status']),
                'credit_unit' => CreditUnit::from((int) $semesterResult['credit_unit']),
                'semester_enrollment_id' => $semesterEnrollment->id,
            ]);

            $inCourse = $semesterResult['in_course_score'];
            $exam = $semesterResult['exam_score'];

            if ($inCourse === '' && $exam === '') {
                continue;
            }

            $score = TotalScore::fromInCourseAndExam(InCourseScore::new((int) $inCourse), ExamScore::new((int) $exam));
            $grade = Grade::for($score);
            $gradePoint = $grade->point() * $registration->credit_unit->value;
            $data = "{$registration->id}-{$score->value}-{$grade->value}-{$gradePoint}";

            $result = Result::query()->create([
                'grade' => $grade->name,
                'grade_point' => $gradePoint,
                'registration_id' => $registration->id,
                'scores' => json_encode(['exam' => $exam, 'in_course' => $inCourse]),
                'total_score' => $score->value,
            ]);

            $result->resultDetail()->create(['value' => $data]);
        }
    }
}
