<?php

declare(strict_types=1);

use App\Data\Download\PortalDateData;
use App\Data\Download\PortalResultData;
use App\Http\Clients\Fakes\FakeResultClient;

it('constructs a correct portal result data from result array', function (): void {
    $result = FakeResultClient::RESULTS[0];

    $data = PortalResultData::from($result);

    expect($data)->toBeInstanceOf(PortalResultData::class)
        ->and($data->onlineId)->toBe($result['id'])
        ->and($data->courseRegistrationId)->toBe($result['course_registration_id'])
        ->and($data->registrationNumber)->toBe($result['registration_number'])
        ->and($data->inCourseScore)->toBe($result['in_course'])
        ->and($data->examScore)->toBe($result['exam_score'])
        ->and($data->totalScore)->toBe($result['total_score'])
        ->and($data->grade)->toBe($result['grade'])
        ->and($data->uploadDate)->toBeInstanceOf(PortalDateData::class);
});
