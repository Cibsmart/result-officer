<?php

declare(strict_types=1);

use App\Http\Clients\PortalCourseRegistrationClient;

beforeEach(function () {
    $this->client = new PortalCourseRegistrationClient();

    $this->expectedKeys = [
        'id', 'registration_number', 'session', 'semester', 'level', 'course_id', 'credit_unit', 'registration_date',
    ];
});

it('can fetch course registrations by registration number from the api', function (): void {
    $registrationNumber = 'EBSU/2009/51486';

    $registrations = $this->client->fetchCourseRegistrationByRegistrationNumber($registrationNumber);

    expect($registrations)->toBeArray()
        ->and($registrations[0])->toHaveKeys($this->expectedKeys)
        ->and($registrations[0]['registration_number'])->toBe($registrationNumber);
})->group('external');

it('can fetch course registrations by department session and level from the api', function (): void {
    $departmentId = '1';
    $session = '2009/2010';
    $level = '100';

    $registrations = $this->client->fetchCourseRegistrationByDepartmentSessionLevel($departmentId, $session, $level);

    expect($registrations)->toBeArray()
        ->and($registrations[0])->toHaveKeys($this->expectedKeys)
        ->and($registrations[0]['session'])->toBe($session)
        ->and($registrations[0]['level'])->toBe($level);
})->group('external');

it('can fetch course registrations by department session and semester from the api', function (): void {
    $departmentId = '1';
    $session = '2009/2010';
    $semester = 'FIRST';

    $registrations = $this->client->fetchCourseRegistrationByDepartmentSessionSemester(
        $departmentId, $session, $semester,
    );

    expect($registrations)->toBeArray()
        ->and($registrations[0])->toHaveKeys($this->expectedKeys)
        ->and($registrations[0]['session'])->toBe($session)
        ->and($registrations[0]['semester'])->toBe($semester);
})->group('external');

it('can fetch course registrations by session and course from the api', function (): void {
    $session = '2009/2010';
    $course = '872';

    $registrations = $this->client->fetchCourseRegistrationBySessionCourse($session, $course);

    expect($registrations)->toBeArray()
        ->and($registrations[0])->toHaveKeys($this->expectedKeys)
        ->and($registrations[0]['session'])->toBe($session)
        ->and($registrations[0]['course_id'])->toBe($course);
})->group('external');

it('throws an exception for non-existent valid registration number', function (): void {
    $this->client->fetchCourseRegistrationByRegistrationNumber('EBSU/0000/00000');

})
    ->throws(Exception::class, 'ERROR FETCHING DATA FROM API:')
    ->group('external');

it('throws an exception for invalid registration', function (): void {
    $this->client->fetchCourseRegistrationByRegistrationNumber('EBUS/209/51486');
})
    ->throws(Exception::class, 'ERROR FETCHING DATA FROM API:')
    ->group('external');

it('throws an exception when connection is available', function (): void {
    Config::set('rp_http.base_url', '');

    $this->client->fetchCourseRegistrationByRegistrationNumber('0');
})
    ->throws(Exception::class, 'ERROR CONNECTING TO API:')
    ->group('external');
