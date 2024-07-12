<?php

declare(strict_types=1);

use App\Data\Results\StudentResultData;
use App\Services\ComputeAverage;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Tests\Factories\CourseRegistrationFactory;
use Tests\Factories\CourseStatusFactory;
use Tests\Factories\EnrollmentFactory;
use Tests\Factories\ResultFactory;
use Tests\Factories\SemesterEnrollmentFactory;
use Tests\Factories\SemesterFactory;
use Tests\Factories\StudentFactory;

test('session result data is correct', function (): void {
    $courseStatus = CourseStatusFactory::new()->create();
    $firstSemester = SemesterFactory::new(['name' => 'FIRST'])->create();
    $secondSemester = SemesterFactory::new(['name' => 'SECOND'])->create();

    $student =
        StudentFactory::new()->has(
            EnrollmentFactory::new()
                ->has(SemesterEnrollmentFactory::new()
                    ->has(CourseRegistrationFactory::new(['course_status_id' => $courseStatus->id])
                        ->has(ResultFactory::new())
                        ->count(5),
                        'courses')
                    ->count(2)
                    ->state(new Sequence(
                        ['semester_id' => $firstSemester->id],
                        ['semester_id' => $secondSemester->id]),
                    ), 'semesters')
                ->count(2),
        )->create();

    $resultData = StudentResultData::from($student);

    $sessionEnrollments = $student->enrollments;

    $cgpaSum = 0;

    foreach ($sessionEnrollments as $sessionEnrollment) {
        $semesterEnrollments = $sessionEnrollment->semesters;

        $gpaSum = 0;

        foreach ($semesterEnrollments as $semesterEnrollment) {
            $courses = $semesterEnrollment->courses;

            $gpaSum += ComputeAverage::new(
                $courses->sum('result.grade_point'),
                $courses->sum('credit_unit'),
            )->value();
        }

        $cgpaSum += ComputeAverage::new($gpaSum, $semesterEnrollments->count())->value();
    }

    $fcgpa = round(ComputeAverage::new($cgpaSum, $sessionEnrollments->count())->value(), 2);

    expect($resultData)->toBeInstanceOf(StudentResultData::class)
        ->and($resultData->id)->toBe($student->id)
        ->and($resultData->enrollments->count())->toBe($sessionEnrollments->count())
        ->and($resultData->finalCumulativeGradePointAverage)->toBe($fcgpa);
});
