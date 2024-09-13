<?php

declare(strict_types=1);

use App\Http\Clients\Fakes\FakeStudentClient;
use App\Http\Clients\PortalStudentClient;
use Illuminate\Support\Facades\Http;

beforeEach(function (): void {
    Http::preventStrayRequests();

    Http::fake([
        'students.ashx?department=1&session=2009-2010' => Http::response([
            'data' => array_values(FakeStudentClient::STUDENTS),
            'message' => 'success',
            'status' => true,
        ]),
        'students.ashx?registration_number=EBSU-2009-51486' => Http::response([
            'data' => [FakeStudentClient::STUDENTS[0]],
            'message' => 'success',
            'status' => true,
        ]),
        'students.ashx?registration_number=EBSU-2011-51486' => Http::response([
            'data' => [],
        ]),
        'students.ashx?session=2009-2010' => Http::response([
            'data' => array_values(FakeStudentClient::STUDENTS),
            'message' => 'success',
            'status' => true,
        ]),
    ]);

    $this->client = new PortalStudentClient();
});

it('can fetch student by registration number', function (): void {
    $student = $this->client->fetchStudentByRegistrationNumber('EBSU-2009-51486')[0];

    expect($student)->toBeArray()
        ->and($student)->toMatchArray(['registration_number' => 'EBSU/2009/51486']);
});

it('can fetch students by department and session', function (): void {
    $departmentId = 1;
    $session = '2009-2010';
    $students = $this->client->fetchStudentsByDepartmentAndSession($departmentId, $session);

    expect($students)->toBeArray()
        ->and(count($students))->toEqual(count(FakeStudentClient::STUDENTS));
});

it('can fetch students by session', function (): void {
    $session = '2009-2010';
    $students = $this->client->fetchStudentsBySession($session);

    expect($students)->toBeArray()
        ->and(count($students))->toEqual(count(FakeStudentClient::STUDENTS));
});
