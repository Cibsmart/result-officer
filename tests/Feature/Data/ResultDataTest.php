<?php

declare(strict_types=1);

use App\Data\Results\ResultData;
use Tests\Factories\CourseRegistrationFactory;
use Tests\Factories\ResultFactory;

test('course result data is correct', function () {
    $course = CourseRegistrationFactory::new()->has(ResultFactory::new())->create();

    $resultData = ResultData::from($course);

    expect($resultData)->toBeInstanceOf(ResultData::class)
        ->and($resultData->id)->toBe($course->id)
        ->and($resultData->courseCode)->toBe($course->course->code)
        ->and($resultData->courseTitle)->toBe($course->course->title)
        ->and($resultData->creditUnit)->toBe($course->credit_unit)
        ->and($resultData->totalScore)->toBe($course->result->total_score)
        ->and($resultData->grade)->toBe($course->result->grade)
        ->and($resultData->gradePoint)->toBe($course->result->grade_point);
});

test('courses without result has default score', function () {
    $course = CourseRegistrationFactory::new()->create();

    $resultData = ResultData::from($course);

    expect($resultData)->toBeInstanceOf(ResultData::class)
        ->and($resultData->totalScore)->toBe($course->result->total_score)
        ->and($resultData->grade)->toBe($course->result->grade)
        ->and($resultData->gradePoint)->toBe($course->result->grade_point);
});
