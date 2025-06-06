<?php

declare(strict_types=1);

use App\Helpers\ComputeAverage;
use App\Models\SemesterEnrollment;
use App\Models\SessionEnrollment;
use App\Models\Student;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Tests\Factories\LevelFactory;
use Tests\Factories\ProgramFactory;
use Tests\Factories\RegistrationFactory;
use Tests\Factories\ResultFactory;
use Tests\Factories\SemesterEnrollmentFactory;
use Tests\Factories\SemesterFactory;
use Tests\Factories\SessionEnrollmentFactory;
use Tests\Factories\SessionFactory;
use Tests\Factories\StudentFactory;

/** @param array<string, string> $attributes */
function createStudentWithResults(
    int $numberOfSessions = 4,
    int $numberOfSemesters = 2,
    int $numberOfCourses = 5,
    array $attributes = [],
): Student {
    $firstSemester = SemesterFactory::new(['name' => 'FIRST'])->createOne();
    $secondSemester = SemesterFactory::new(['name' => 'SECOND'])->createOne();

    return
        StudentFactory::new()->has(
            SessionEnrollmentFactory::new()
                ->has(SemesterEnrollmentFactory::new()
                    ->has(RegistrationFactory::new()
                        ->has(ResultFactory::new())
                        ->count($numberOfCourses),
                        'registrations')
                    ->count($numberOfSemesters)
                    ->state(new Sequence(
                        ['semester_id' => $firstSemester->id],
                        ['semester_id' => $secondSemester->id]),
                    ), 'semesterEnrollments')
                ->count($numberOfSessions),
        )->createOne($attributes);
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
            SessionEnrollmentFactory::new(['level_id' => $level->id, 'session_id' => $session->id])
                ->has(SemesterEnrollmentFactory::new()
                    ->has(RegistrationFactory::new()
                        ->has(ResultFactory::new())
                        ->count($numberOfCourses),
                        'registrations')
                    ->count($numberOfSemesters)
                    ->state(new Sequence(
                        ['semester_id' => $firstSemester->id],
                        ['semester_id' => $secondSemester->id]),
                    ), 'semesterEnrollments')
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
    $registrations = $semesterEnrollment->registrations;

    return ComputeAverage::new(
        $registrations->sum('result.grade_point'),
        $registrations->sum('credit_unit.value'),
    )->value();
}

function computeCGPA(SessionEnrollment $sessionEnrollment): float
{
    $semesterEnrollments = $sessionEnrollment->semesterEnrollments;

    $gpaSum = 0;

    foreach ($semesterEnrollments as $semesterEnrollment) {
        $gpaSum += computeGPA($semesterEnrollment);
    }

    return ComputeAverage::new($gpaSum, $semesterEnrollments->count())->value();
}

function computeFCGPA(Student $student): float
{
    $sessionEnrollments = $student->sessionEnrollments()
        ->with('semesterEnrollments.registrations.result')
        ->get();

    $cgpaSum = 0;

    foreach ($sessionEnrollments as $sessionEnrollment) {
        $cgpaSum += computeCGPA($sessionEnrollment);
    }

    return round(ComputeAverage::new($cgpaSum, $sessionEnrollments->count())->value(), 2);
}
