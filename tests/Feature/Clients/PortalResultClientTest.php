<?php

declare(strict_types=1);

use App\Http\Clients\PortalResultClient;

beforeEach(function () {
    $this->client = new PortalResultClient();

    $this->expectedKeys = [
        'id', 'course_registration_id', 'registration_number', 'in_course', 'exam_score', 'total_score', 'grade',
        'upload_date', 'exam_date', 'lecturer_name', 'lecturer_department',
    ];
});

it('can fetch result by course registration id from the api', function (): void {
    $courseRegistrationId = '1769449';

    $results = $this->client->fetchResultByCourseRegistrationId($courseRegistrationId);

    expect($results)->toBeArray()
        ->and($results[0])->toHaveKeys($this->expectedKeys)
        ->and($results[0]['course_registration_id'])->toBe($courseRegistrationId);
})->group('external');

it('can fetch results by registration number from the api', function (): void {
    $registrationNumber = 'EBSU/2009/51486';

    $results = $this->client->fetchResultsByRegistrationNumber($registrationNumber);

    expect($results)->toBeArray()
        ->and($results[0])->toHaveKeys($this->expectedKeys)
        ->and($results[0]['registration_number'])->toBe($registrationNumber);
})->group('external');

it('can fetch results by department session and level from the api', function (): void {
    $departmentId = '1';
    $session = '2009/2010';
    $level = '100';

    $results = $this->client->fetchResultsByDepartmentSessionLevel($departmentId, $session, $level);

    expect($results)->toBeArray()
        ->and($results[0])->toHaveKeys($this->expectedKeys);
})->group('external');

it('can fetch results by department session and semester from the api', function (): void {
    $departmentId = '1';
    $session = '2009/2010';
    $semester = 'FIRST';

    $results = $this->client->fetchResultsByDepartmentSessionSemester(
        $departmentId, $session, $semester,
    );

    expect($results)->toBeArray()
        ->and($results[0])->toHaveKeys($this->expectedKeys);
})->group('external');

it('can fetch results by session and course from the api', function (): void {
    $session = '2009/2010';
    $course = '872';

    $results = $this->client->fetchResultsBySessionCourse($session, $course);

    expect($results)->toBeArray()
        ->and($results[0])->toHaveKeys($this->expectedKeys);
})->group('external');

it('throws an exception for non-existent valid registration number', function (): void {
    $this->client->fetchResultsByRegistrationNumber('EBSU/0000/00000');

})
    ->throws(Exception::class, 'ERROR FETCHING DATA FROM API:')
    ->group('external');

it('throws an exception for invalid registration', function (): void {
    $this->client->fetchResultsByRegistrationNumber('EBUS/209/51486');
})
    ->throws(Exception::class, 'ERROR FETCHING DATA FROM API:')
    ->group('external');

it('throws an exception when connection is available', function (): void {
    Config::set('rp_http.base_url', '');

    $this->client->fetchResultsByRegistrationNumber('0');
})
    ->throws(Exception::class, 'ERROR CONNECTING TO API:')
    ->group('external');
