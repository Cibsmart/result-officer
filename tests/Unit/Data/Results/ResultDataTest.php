<?php

declare(strict_types=1);

use App\Data\Results\ResultData;
use Tests\Factories\RegistrationFactory;
use Tests\Factories\ResultFactory;

test('course result data is correct', function (): void {

    $course = RegistrationFactory::new()
        ->has(ResultFactory::new())->createOne();

    $resultData = ResultData::from($course);

    expect($resultData)->toBeInstanceOf(ResultData::class)
        ->and($resultData->id)->toBe($course->id)
        ->and($resultData->courseCode)->toBe($course->course->code)
        ->and($resultData->courseTitle)->toBe($course->course->title)
        ->and($resultData->creditUnit)->toBe($course->credit_unit->value)
        ->and($resultData->totalScore)->toBe($course->result->total_score)
        ->and($resultData->grade)->toBe($course->result->grade)
        ->and($resultData->gradePoint)->toBe($course->result->grade_point);
});

test('courses without result has default score', function (): void {
    $course = RegistrationFactory::new()->createOne();

    $resultData = ResultData::from($course);

    expect($resultData)->toBeInstanceOf(ResultData::class)
        ->and($resultData->totalScore)->toBe(0)
        ->and($resultData->grade)->toBe('F')
        ->and($resultData->gradePoint)->toBe(0)
        ->and($resultData->remark)->toBe('NR');
});
