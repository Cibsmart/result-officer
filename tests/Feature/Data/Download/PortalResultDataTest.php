<?php

declare(strict_types=1);

use App\Data\Download\PortalDateData;
use App\Data\Download\PortalResultData;
use App\Enums\Grade;
use App\Http\Clients\Fakes\FakeResultClient;
use App\Values\ExamScore;
use App\Values\InCourseScore;
use App\Values\RegistrationNumber;
use App\Values\TotalScore;

it('constructs a correct portal result data from result array', function (): void {
    $result = FakeResultClient::RESULTS[0];

    $data = PortalResultData::from($result);

    expect($data)->toBeInstanceOf(PortalResultData::class)
        ->and($data->onlineId)->toBe($result['id'])
        ->and($data->courseRegistrationId)->toBe($result['course_registration_id'])
        ->and($data->registrationNumber)->toBeInstanceOf(RegistrationNumber::class)
        ->and($data->registrationNumber->value)->toBe($result['registration_number'])
        ->and($data->inCourseScore)->toBeInstanceOf(InCourseScore::class)
        ->and($data->inCourseScore->value)->toBe((int) $result['in_course'])
        ->and($data->examScore)->toBeInstanceOf(ExamScore::class)
        ->and($data->examScore->value)->toBe((int) $result['exam_score'])
        ->and($data->totalScore)->toBeInstanceOf(TotalScore::class)
        ->and($data->totalScore->value)->toBe((int) $result['total_score'])
        ->and($data->grade)->toBeInstanceOf(Grade::class)
        ->and($data->grade->name)->toBe($result['grade'])
        ->and($data->uploadDate)->toBeInstanceOf(PortalDateData::class);
});

it('throws invalid argument exception for invalid scores', function (string $key, string $value): void {
    $result = FakeResultClient::RESULTS[0];

    $result[$key] = $value;

    PortalResultData::from($result);
})
    ->with([
        ['in_course', '51'],
        ['exam_score', '101'],
    ])
    ->throws(InvalidArgumentException::class);

it('throws invalid argument exception for invalid registration', function (string $key, string $value): void {
    $result = FakeResultClient::RESULTS[0];

    $result[$key] = $value;

    PortalResultData::from($result);
})
    ->with([
        ['registration_number', 'EBSU/209/51486'],
    ])
    ->throws(InvalidArgumentException::class);
