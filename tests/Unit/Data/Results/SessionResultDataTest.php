<?php

declare(strict_types=1);

use App\Data\Results\SessionResultData;
use App\Helpers\ComputeAverage;
use Tests\Factories\RegistrationFactory;
use Tests\Factories\ResultFactory;
use Tests\Factories\SemesterEnrollmentFactory;
use Tests\Factories\SessionEnrollmentFactory;
use Tests\Factories\StudentFactory;

test('session result data is correct', function (): void {
    $student = StudentFactory::new()->createOne();

    $sessionEnrollment = SessionEnrollmentFactory::new(['student_id' => $student->id])
        ->has(SemesterEnrollmentFactory::new()
            ->has(RegistrationFactory::new()
                ->has(ResultFactory::new())
                ->count(5),
                'registrations')
            ->count(2),
            'semesterEnrollments')
        ->createOne();

    $sessionData = SessionResultData::from($sessionEnrollment);

    $semestersResults = $sessionEnrollment->semesterEnrollments()
        ->with('registrations.result')
        ->get();

    $gradePointAverageTotal = 0;

    foreach ($semestersResults as $semesterResult) {
        $courses = $semesterResult->registrations;
        $gradePointAverageTotal +=
            ComputeAverage::new(
                $courses->sum('result.grade_point'),
                $courses->sum('credit_unit.value'),
            )->value();
    }

    $cgpa = ComputeAverage::new($gradePointAverageTotal, $semestersResults->count())->value();
    $cgpaFormatted = number_format($cgpa, 3);

    expect($sessionData)->toBeInstanceOf(SessionResultData::class)
        ->and($sessionData->id)->toBe($sessionEnrollment->id)
        ->and($sessionData->semesterResults->count())->toBe($semestersResults->count())
        ->and($sessionData->session)->toBe($sessionEnrollment->session->name)
        ->and($sessionData->year)->toBe($sessionEnrollment->year->name)
        ->and($sessionData->cumulativeGradePointAverage)->toBe($cgpa)->toBeFloat()
        ->and($sessionData->formattedCGPA)->toBe($cgpaFormatted)->toBeString();
});

test('session enrollment without result data returns zeroes', function (): void {
    $student = StudentFactory::new()->createOne();

    $sessionEnrollment = SessionEnrollmentFactory::new(['student_id' => $student->id])
        ->has(SemesterEnrollmentFactory::new()
            ->has(RegistrationFactory::new(), 'registrations')
            ->count(2),
            'semesterEnrollments')
        ->createOne();

    $sessionData = SessionResultData::from($sessionEnrollment);

    $semestersResults = $sessionEnrollment->semesterEnrollments;

    expect($sessionData)->toBeInstanceOf(SessionResultData::class)
        ->and($sessionData->id)->toBe($sessionEnrollment->id)
        ->and($sessionData->semesterResults->count())->toBe($semestersResults->count())
        ->and($sessionData->session)->toBe($sessionEnrollment->session->name)
        ->and($sessionData->cumulativeGradePointAverage)->toBe(0.0)
        ->and($sessionData->formattedCGPA)->toBe('0.000')->toBeString();

});

test('session enrollment without semester enrollments returns zeroes', function (): void {
    $student = StudentFactory::new()->createOne();

    $sessionEnrollment = SessionEnrollmentFactory::new(['student_id' => $student->id])
        ->createOne();

    $sessionData = SessionResultData::from($sessionEnrollment);

    $semestersResults = $sessionEnrollment->semesterEnrollments;

    expect($sessionData)->toBeInstanceOf(SessionResultData::class)
        ->and($sessionData->id)->toBe($sessionEnrollment->id)
        ->and($sessionData->semesterResults->count())->toBe($semestersResults->count())
        ->and($sessionData->session)->toBe($sessionEnrollment->session->name)
        ->and($sessionData->cumulativeGradePointAverage)->toBe(0.0)
        ->and($sessionData->formattedCGPA)->toBe('0.000')->toBeString();

});

it('student without enrollment returns zero sessional results count', function (): void {
    $sessionEnrollment = SessionEnrollmentFactory::new()->createOne();

    $sessionData = SessionResultData::from($sessionEnrollment);

    expect($sessionData->resultsCount)->toBe(0);
});

it('returns students sessional results count', function (): void {
    $student = StudentFactory::new()->createOne();

    $sessionEnrollment = SessionEnrollmentFactory::new(['student_id' => $student->id])
        ->has(SemesterEnrollmentFactory::new()
            ->has(RegistrationFactory::new()
                ->has(ResultFactory::new())
                ->count(5),
                'registrations')
            ->count(2),
            'semesterEnrollments')
        ->createOne();

    $sessionData = SessionResultData::from($sessionEnrollment);

    expect($sessionData->resultsCount)->toBe($sessionEnrollment->registrations->count());
});
