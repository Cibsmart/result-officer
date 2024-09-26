<?php

declare(strict_types=1);

use App\Data\Download\PortalDateData;
use App\Data\Download\PortalRegistrationData;
use App\Http\Clients\Fakes\FakeCourseRegistrationClient;

test('portal student data is correct', function (): void {
    $registration = FakeCourseRegistrationClient::COURSE_REGISTRATIONS[0];
    $data = PortalRegistrationData::from($registration);

    expect($data)->toBeInstanceOf(PortalRegistrationData::class)
        ->and($data->registrationNumber)->toEqual($registration['registration_number'])
        ->and($data->registrationDate)->toBeInstanceOf(PortalDateData::class)
        ->and($data->session)->toBe($registration['session'])
        ->and($data->semester)->toBe($registration['semester'])
        ->and($data->level)->toBe($registration['level']);
});
