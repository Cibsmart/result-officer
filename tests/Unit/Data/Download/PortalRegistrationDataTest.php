<?php

declare(strict_types=1);

use App\Data\Download\PortalRegistrationData;
use App\Http\Clients\Fakes\FakeRegistrationClient;

test('portal student data is correct', function (): void {
    $registration = FakeRegistrationClient::REGISTRATIONS[0];
    $data = PortalRegistrationData::from($registration);

    expect($data)->toBeInstanceOf(PortalRegistrationData::class)
        ->and($data->registrationNumber)->toEqual($registration['registration_number'])
        ->and($data->registrationDate)->toBe($registration['registration_date'])
        ->and($data->session)->toBe($registration['session'])
        ->and($data->semester)->toBe($registration['semester'])
        ->and($data->level)->toBe($registration['level']);
});
