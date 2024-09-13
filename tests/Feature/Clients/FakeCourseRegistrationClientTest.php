<?php

declare(strict_types=1);

use App\Http\Clients\Fakes\FakeCourseRegistrationClient;
use App\Http\Clients\PortalCourseRegistrationClient;
use Illuminate\Support\Facades\Http;

beforeEach(function (): void {
    Http::preventStrayRequests();

    Http::fake([
        'course-registrations.ashx?department=1&session=2009-2010&level=100' => Http::response([
            'data' => FakeCourseRegistrationClient::COURSE_REGISTRATIONS,
            'message' => 'success',
            'status' => true,
        ]),
        'course-registrations.ashx?department=1&session=2009-2010&semester=FIRST' => Http::response([
            'data' => FakeCourseRegistrationClient::COURSE_REGISTRATIONS,
            'message' => 'success',
            'status' => true,
        ]),
        'course-registrations.ashx?registration_number=EBSU-2009-51486' => Http::response([
            'data' => FakeCourseRegistrationClient::COURSE_REGISTRATIONS,
            'message' => 'success',
            'status' => true,
        ]),
        'course-registrations.ashx?registration_number=EBSU-2011-51486' => Http::response([
            'data' => [],
            'message' => 'Record Not Found',
            'status' => false,
        ]),
        'course-registrations.ashx?session=2009-2010&course_id=1' => Http::response([
            'data' => FakeCourseRegistrationClient::COURSE_REGISTRATIONS,
            'message' => 'success',
            'status' => true,
        ]),
    ]);

    $this->client = new PortalCourseRegistrationClient();
});

it('can fetch course registrations by registration number', function (): void {
    $registration = $this->client->fetchCourseRegistrationByRegistrationNumber('EBSU-2009-51486');

    expect($registration)->toBeArray()
        ->and(count($registration))->toBe(count(FakeCourseRegistrationClient::COURSE_REGISTRATIONS))
        ->and($registration[0])->toMatchArray(['registration_number' => 'EBSU/2009/51486']);
});

it('can fetch course registration by department and session and level', function (): void {
    $departmentId = 1;
    $session = '2009-2010';
    $level = 100;
    $registrations = $this->client->fetchCourseRegistrationByDepartmentSessionLevel($departmentId, $session, $level);

    expect($registrations)->toBeArray()
        ->and(count($registrations))->toEqual(count(FakeCourseRegistrationClient::COURSE_REGISTRATIONS));
});

it('can fetch course registration by department and session and semester', function (): void {
    $departmentId = 1;
    $session = '2009-2010';
    $semester = 'FIRST';
    $registrations = $this->client->fetchCourseRegistrationByDepartmentSessionSemester($departmentId, $session,
        $semester);

    expect($registrations)->toBeArray()
        ->and(count($registrations))->toEqual(count(FakeCourseRegistrationClient::COURSE_REGISTRATIONS));
});

it('can fetch course registration by session and course', function (): void {
    $session = '2009-2010';
    $courseId = 1;
    $registration = $this->client->fetchCourseRegistrationBySessionCourse($session, $courseId);

    expect($registration)->toBeArray()
        ->and(count($registration))->toEqual(count(FakeCourseRegistrationClient::COURSE_REGISTRATIONS));
});

it('throws an exception for a non-existent student registration number', function (): void {
    $this->client->fetchCourseRegistrationByRegistrationNumber('EBSU-2011-51486');
})->throws(Exception::class, 'API RETURNED ERROR: Record Not Found');
