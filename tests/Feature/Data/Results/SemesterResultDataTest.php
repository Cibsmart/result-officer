<?php

declare(strict_types=1);

use App\Data\Results\SemesterResultData;
use Tests\Factories\CourseRegistrationFactory;
use Tests\Factories\CourseStatusFactory;
use Tests\Factories\ResultFactory;
use Tests\Factories\SemesterEnrollmentFactory;

test('semester result data is correct', function (): void {

    $courseStatus = CourseStatusFactory::new()->create();

    $enrollment = SemesterEnrollmentFactory::new()
        ->has(CourseRegistrationFactory::new(['course_status_id' => $courseStatus->id])
            ->has(ResultFactory::new())
            ->count(5), 'courses')
        ->create();

    $semesterData = SemesterResultData::from($enrollment);

    $courses = $enrollment->courses;

    $totalCU = $courses->sum('credit_unit');
    $totalGP = $courses->sum('result.grade_point');
    $gpa = round($totalGP / $totalCU, 3);

    expect($semesterData)->toBeInstanceOf(SemesterResultData::class)
        ->and($semesterData->id)->toBe($enrollment->id)
        ->and($semesterData->results->count())->toBe($courses->count())
        ->and($semesterData->creditUnitTotal)->toBe($totalCU)
        ->and($semesterData->gradePointTotal)->toBe($totalGP)
        ->and($semesterData->gradePointAverage)->toBe($gpa);

});

test('semester enrollment without result data returns zeroes', function (): void {
    $enrollment = SemesterEnrollmentFactory::new()->create();

    $semesterData = SemesterResultData::from($enrollment);

    expect($semesterData)->toBeInstanceOf(SemesterResultData::class)
        ->and($semesterData->gradePointTotal)->toBe(0)
        ->and($semesterData->creditUnitTotal)->toBe(0)
        ->and($semesterData->gradePointAverage)->toBe(0.00);

});
