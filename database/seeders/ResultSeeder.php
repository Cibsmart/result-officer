<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\CourseStatus;
use App\Enums\CreditUnit;
use App\Enums\Grade;
use App\Enums\Year;
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
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

final class ResultSeeder extends Seeder
{
    public function run(): void
    {
        $content = Storage::get('seeders/results.csv');

        assert(! is_null($content));

        $lines = explode("\n", $content);

        $header = [];

        $currentStudent = '';
        $currentSession = '';
        $currentSemester = '';

        $student = null;
        $sessionEnrollment = null;
        $semesterEnrollment = null;

        foreach ($lines as $index => $line) {
            /** @var array<int, string> $data */
            $data = str_getcsv($line);

            if ($index === 0) {
                $header = collect($data)->map(fn ($value) => Str::slug($value, '_'))->all();

                continue;
            }

            /** @var array<string, string> $item */
            $item = array_combine($header, $data);

            if ($currentStudent !== $item['registration_number']) {

                $currentStudent = $item['registration_number'];

                $student = Student::query()->where('registration_number', $currentStudent)->firstOrFail();
            }

            if ($currentSession !== $item['session']) {

                $currentSession = $item['session'];

                $sessionEnrollment = SessionEnrollment::query()->firstOrCreate([
                    'level_id' => Level::getUsingName($item['level'])->id,
                    'session_id' => Session::getUsingName($currentSession)->id,
                    'student_id' => $student->id,
                    'year' => Year::FIRST,
                ]);
            }

            if ($currentSemester !== $item['semester']) {

                $currentSemester = $item['semester'];

                $semesterEnrollment = SemesterEnrollment::query()->firstOrCreate([
                    'semester_id' => Semester::getUsingName($currentSemester)->id,
                    'session_enrollment_id' => $sessionEnrollment->id,
                ]);
            }

            $course = Course::getUsingCode($item['course_code']);

            $registration = Registration::query()->create([
                'course_id' => $course->id,
                'course_status' => CourseStatus::from($item['course_status']),
                'credit_unit' => CreditUnit::from((int) $item['credit_unit']),
                'semester_enrollment_id' => $semesterEnrollment->id,
            ]);

            $score = TotalScore::fromInCourseAndExam(InCourseScore::new((int) $item['in_course_score']),
                ExamScore::new((int) $item['exam_score']));
            $grade = Grade::for($score);
            $gradePoint = $grade->point() * $registration->credit_unit->value;
            $data = "{$registration->id}-{$score->value}-{$grade->value}-{$gradePoint}";

            Result::query()->create([
                'data' => $data,
                'grade' => $grade->name,
                'grade_point' => $gradePoint,
                'registration_id' => $registration->id,
                'scores' => json_encode(['in-course' => $item['in_course_score'], 'exam' => $item['exam_score']]),
                'total_score' => $score->value,
            ]);
        }
    }
}
