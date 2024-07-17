<?php

declare(strict_types=1);

use App\Models\Enrollment;
use App\Models\SemesterEnrollment;
use App\Models\Student;
use App\Services\ComputeAverage;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Tests\Factories\CourseRegistrationFactory;
use Tests\Factories\CourseStatusFactory;
use Tests\Factories\EnrollmentFactory;
use Tests\Factories\ResultFactory;
use Tests\Factories\SemesterEnrollmentFactory;
use Tests\Factories\SemesterFactory;
use Tests\Factories\StudentFactory;

function createStudentWithResults(
    int $numberOfSessions = 4,
    int $numberOfSemesters = 2,
    int $numberOfCourses = 5,
): Student {
    $courseStatus = CourseStatusFactory::new()->create();
    $firstSemester = SemesterFactory::new(['name' => 'FIRST'])->create();
    $secondSemester = SemesterFactory::new(['name' => 'SECOND'])->create();

    return
        StudentFactory::new()->has(
            EnrollmentFactory::new()
                ->has(SemesterEnrollmentFactory::new()
                    ->has(CourseRegistrationFactory::new(['course_status_id' => $courseStatus->id])
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
