<?php

declare(strict_types=1);

use App\Data\Results\SessionResultData;
use App\Helpers\ComputeAverage;
use Tests\Factories\CourseRegistrationFactory;
use Tests\Factories\EnrollmentFactory;
use Tests\Factories\ResultFactory;
use Tests\Factories\SemesterEnrollmentFactory;
use Tests\Factories\StudentFactory;

test('session result data is correct', function (): void {
    $student = StudentFactory::new()->createOne();

    $enrollment = EnrollmentFactory::new(['student_id' => $student->id])
        ->has(SemesterEnrollmentFactory::new()
            ->has(CourseRegistrationFactory::new()
                ->has(ResultFactory::new())
                ->count(5),
                'courses')
            ->count(2),
            'semesters')
        ->createOne();

    $sessionData = SessionResultData::from($enrollment);

    $semestersResults = $enrollment->semesters;

    $gradePointAverageTotal = 0;

    foreach ($semestersResults as $semesterResult) {
        $courses = $semesterResult->courses;
        $gradePointAverageTotal +=
            ComputeAverage::new(
                $courses->sum('result.grade_point'),
                $courses->sum('credit_unit'),
            )->value();
    }

    $cgpa = ComputeAverage::new($gradePointAverageTotal, $semestersResults->count())->value();
    $cgpaFormatted = number_format($cgpa, 3);

    expect($sessionData)->toBeInstanceOf(SessionResultData::class)
        ->and($sessionData->id)->toBe($enrollment->id)
        ->and($sessionData->semesterResults->count())->toBe($semestersResults->count())
        ->and($sessionData->session)->toBe($enrollment->session->name)
        ->and($sessionData->year)->toBe($enrollment->year->name)
        ->and($sessionData->cumulativeGradePointAverage)->toBe($cgpa)->toBeFloat()
        ->and($sessionData->formattedCGPA)->toBe($cgpaFormatted)->toBeString();
});

test('session enrollment without result data returns zeroes', function (): void {
    $student = StudentFactory::new()->createOne();

    $enrollment = EnrollmentFactory::new(['student_id' => $student->id])
        ->has(SemesterEnrollmentFactory::new()
            ->has(CourseRegistrationFactory::new(), 'courses')
            ->count(2),
            'semesters')
        ->createOne();

    $sessionData = SessionResultData::from($enrollment);

    $semestersResults = $enrollment->semesters;

    expect($sessionData)->toBeInstanceOf(SessionResultData::class)
        ->and($sessionData->id)->toBe($enrollment->id)
        ->and($sessionData->semesterResults->count())->toBe($semestersResults->count())
        ->and($sessionData->session)->toBe($enrollment->session->name)
        ->and($sessionData->cumulativeGradePointAverage)->toBe(0.0)
        ->and($sessionData->formattedCGPA)->toBe('0.000')->toBeString();

});

test('session enrollment without semester enrollments returns zeroes', function (): void {
    $student = StudentFactory::new()->createOne();

    $enrollment = EnrollmentFactory::new(['student_id' => $student->id])
        ->createOne();

    $sessionData = SessionResultData::from($enrollment);

    $semestersResults = $enrollment->semesters;

    expect($sessionData)->toBeInstanceOf(SessionResultData::class)
        ->and($sessionData->id)->toBe($enrollment->id)
        ->and($sessionData->semesterResults->count())->toBe($semestersResults->count())
        ->and($sessionData->session)->toBe($enrollment->session->name)
        ->and($sessionData->cumulativeGradePointAverage)->toBe(0.0)
        ->and($sessionData->formattedCGPA)->toBe('0.000')->toBeString();

});
