<?php

declare(strict_types=1);

use App\Helpers\ComputeAverage;
use App\Models\Enrollment;
use App\Models\SemesterEnrollment;
use App\Models\Student;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Tests\Factories\EnrollmentFactory;
use Tests\Factories\LevelFactory;
use Tests\Factories\ProgramFactory;
use Tests\Factories\RegistrationFactory;
use Tests\Factories\ResultFactory;
use Tests\Factories\SemesterEnrollmentFactory;
use Tests\Factories\SemesterFactory;
use Tests\Factories\SessionFactory;
use Tests\Factories\StudentFactory;

function createStudentWithResults(
    int $numberOfSessions = 4,
    int $numberOfSemesters = 2,
    int $numberOfCourses = 5,
): Student {
    $firstSemester = SemesterFactory::new(['name' => 'FIRST'])->createOne();
    $secondSemester = SemesterFactory::new(['name' => 'SECOND'])->createOne();

    return
        StudentFactory::new()->has(
            EnrollmentFactory::new()
                ->has(SemesterEnrollmentFactory::new()
                    ->has(RegistrationFactory::new()
                        ->has(ResultFactory::new())
                        ->count($numberOfCourses),
                        'courses')
                    ->count($numberOfSemesters)
                    ->state(new Sequence(
                        ['semester_id' => $firstSemester->id],
                        ['semester_id' => $secondSemester->id]),
                    ), 'semesters')
                ->count($numberOfSessions),
        )->createOne();
}

function createMultipleStudentsWithResults(
    int $numberOfStudents = 4,
    int $numberOfSessions = 1,
    int $numberOfSemesters = 2,
    int $numberOfCourses = 5,
): Collection {
    $firstSemester = SemesterFactory::new(['name' => 'FIRST'])->createOne();
    $secondSemester = SemesterFactory::new(['name' => 'SECOND'])->createOne();
    $program = ProgramFactory::new()->createOne();
    $session = SessionFactory::new()->createOne();
    $level = LevelFactory::new()->createOne();

    return
        StudentFactory::new()->has(
            EnrollmentFactory::new(['level_id' => $level->id, 'session_id' => $session->id])
                ->has(SemesterEnrollmentFactory::new()
                    ->has(RegistrationFactory::new()
                        ->has(ResultFactory::new())
                        ->count($numberOfCourses),
                        'courses')
                    ->count($numberOfSemesters)
                    ->state(new Sequence(
                        ['semester_id' => $firstSemester->id],
                        ['semester_id' => $secondSemester->id]),
                    ), 'semesters')
                ->count($numberOfSessions),
        )
            ->count($numberOfStudents)
            ->create([
                'entry_level_id' => $level->id,
                'entry_session_id' => $session->id,
                'program_id' => $program->id,
            ]);
}

function computeGPA(SemesterEnrollment $semesterEnrollment): float
{
    $courses = $semesterEnrollment->courses;

    return ComputeAverage::new(
        $courses->sum('result.grade_point'),
        $courses->sum('credit_unit'),
    )->value();
}

function computeCGPA(Enrollment $sessionEnrollment): float
{
    $semesterEnrollments = $sessionEnrollment->semesters;

    $gpaSum = 0;

    foreach ($semesterEnrollments as $semesterEnrollment) {
        $gpaSum += computeGPA($semesterEnrollment);
    }

    return ComputeAverage::new($gpaSum, $semesterEnrollments->count())->value();
}

function computeFCGPA(Student $student): float
{
    $sessionEnrollments = $student->enrollments;

    $cgpaSum = 0;

    foreach ($sessionEnrollments as $sessionEnrollment) {
        $cgpaSum += computeCGPA($sessionEnrollment);
    }

    return round(ComputeAverage::new($cgpaSum, $sessionEnrollments->count())->value(), 2);
}
