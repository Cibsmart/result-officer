<?php

declare(strict_types=1);

use App\Http\Clients\PortalStudentClient;

beforeEach(function (): void {
    $this->client = new PortalStudentClient();

    $this->expectedKeys = [
        'id', 'last_name', 'first_name', 'other_names', 'registration_number', 'gender', 'date_of_birth',
        'department_id', 'option', 'state', 'local_government', 'entry_session', 'entry_mode', 'entry_level',
        'jamb_registration_number', 'phone_number', 'email',
    ];
});

it('can fetch students by registration number from the api', function (): void {
    $registrationNumber = 'EBSU/2009/51486';

    $students = $this->client->fetchStudentByRegistrationNumber($registrationNumber);

    expect($students)->toBeArray()
        ->and($students[0])->toHaveKeys($this->expectedKeys)
        ->and($students[0]['registration_number'])->toBe($registrationNumber);
})->group('external');

it('can fetch students by department and session from the api', function (): void {
    $departmentId = '1';
    $session = '2009/2010';

    $students = $this->client->fetchStudentsByDepartmentAndSession($departmentId, $session);

    expect($students)->toBeArray()
        ->and($students[0])->toHaveKeys($this->expectedKeys)
        ->and($students[0]['department_id'])->toBe($departmentId)
        ->and($students[0]['entry_session'])->toBe($session);
})->group('external');

it('can fetch students by session from the api', function (): void {
    $session = '2009/2010';

    $students = $this->client->fetchStudentsBySession($session);

    expect($students)->toBeArray()
        ->and($students[0])->toHaveKeys($this->expectedKeys)
        ->and($students[0]['entry_session'])->toBe($session);
})->group('external');

it('throws an exception for non-existent valid registration number', function (): void {
    $this->client->fetchStudentByRegistrationNumber('EBSU/0000/00000');

})
    ->throws(Exception::class, 'ERROR FETCHING DATA FROM API:')
    ->group('external');

it('throws an exception for invalid registration', function (): void {
    $this->client->fetchStudentByRegistrationNumber('EBUS/209/51486');
})
    ->throws(Exception::class, 'ERROR FETCHING DATA FROM API:')
    ->group('external');

it('throws an exception when connection is available', function (): void {
    Config::set('rp_http.base_url', '');

    $this->client->fetchStudentByRegistrationNumber('0');
})
    ->throws(Exception::class, 'ERROR CONNECTING TO API:')
    ->group('external');
