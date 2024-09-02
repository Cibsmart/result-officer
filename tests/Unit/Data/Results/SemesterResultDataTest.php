<?php

declare(strict_types=1);

use App\Data\Results\SemesterResultData;
use Tests\Factories\CourseRegistrationFactory;
use Tests\Factories\ResultFactory;
use Tests\Factories\SemesterEnrollmentFactory;

test('semester result data is correct', function (): void {

    $enrollment = SemesterEnrollmentFactory::new()
        ->has(CourseRegistrationFactory::new()
            ->has(ResultFactory::new())
            ->count(5), 'courses')
        ->create();

    $semesterData = SemesterResultData::from($enrollment);

    $courses = $enrollment->courses;

    $totalCU = $courses->sum('credit_unit');
    $totalGP = $courses->sum('result.grade_point');
    $gpa = round($totalGP / $totalCU, 3);
    $totalCUFormatted = str($totalCU)->padLeft(2, '0')->value();
    $totalGPFormatted = str($totalGP)->padLeft(2, '0')->value();
    $gpaFormatted = number_format($gpa, 3);

    expect($semesterData)->toBeInstanceOf(SemesterResultData::class)
        ->and($semesterData->id)->toBe($enrollment->id)
        ->and($semesterData->results->count())->toBe($courses->count())
        ->and($semesterData->creditUnitTotal)->toBe($totalCU)->toBeInt()
        ->and($semesterData->gradePointTotal)->toBe($totalGP)->toBeInt()
        ->and($semesterData->gradePointAverage)->toBe($gpa)->toBeFloat()
        ->and($semesterData->formattedCreditUnitTotal)->toBe($totalCUFormatted)->toBeString()
        ->and($semesterData->formattedGradePointTotal)->toBe($totalGPFormatted)->toBeString()
        ->and($semesterData->formattedGPA)->toBe($gpaFormatted)->toBeString();

});

test('semester enrollment without result data returns zeroes', function (): void {
    $enrollment = SemesterEnrollmentFactory::new()->create();

    $semesterData = SemesterResultData::from($enrollment);

    expect($semesterData)->toBeInstanceOf(SemesterResultData::class)
        ->and($semesterData->gradePointTotal)->toBe(0)->toBeInt()
        ->and($semesterData->formattedGradePointTotal)->toBe('00')->toBeString()
        ->and($semesterData->creditUnitTotal)->toBe(0)->toBeInt()
        ->and($semesterData->formattedCreditUnitTotal)->toBe('00')->toBeString()
        ->and($semesterData->gradePointAverage)->toBe(0.000)->toBeFloat()
        ->and($semesterData->formattedGPA)->toBeString('0.000');
});
