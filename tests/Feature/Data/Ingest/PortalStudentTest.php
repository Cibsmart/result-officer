<?php

declare(strict_types=1);

use App\Data\Download\PortalDateData;
use App\Data\Download\PortalStudentData;
use App\Http\Clients\Fakes\FakeStudentClient;

test('portal student data is correct', function (): void {
    $student = FakeStudentClient::STUDENTS['EBSU-2009-51486'];
    $data = PortalStudentData::from($student);

    expect($data)->toBeInstanceOf(PortalStudentData::class)
        ->and($data->registrationNumber)->toEqual($student['registration_number'])
        ->and($data->dateOfBirth)->toBeInstanceOf(PortalDateData::class)
        ->and($data->gender)->toBe($student['gender']);
});
