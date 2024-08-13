<?php

declare(strict_types=1);

use App\Data\Download\PortalCourseRegistrationData;
use App\Data\Download\PortalDateData;
use App\Http\Clients\Fakes\FakeCourseRegistrationClientClient;

test('portal student data is correct', function (): void {
    $registration = FakeCourseRegistrationClientClient::COURSE_REGISTRATIONS[0];
    $data = PortalCourseRegistrationData::from($registration);

    expect($data)->toBeInstanceOf(PortalCourseRegistrationData::class)
        ->and($data->registrationNumber)->toEqual($registration['registration_number'])
        ->and($data->registrationDate)->toBeInstanceOf(PortalDateData::class)
        ->and($data->session)->toBe($registration['session'])
        ->and($data->semester)->toBe($registration['semester'])
        ->and($data->level)->toBe($registration['level']);
});
