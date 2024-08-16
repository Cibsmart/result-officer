<?php

declare(strict_types=1);

use App\Http\Clients\Fakes\FakeResultClient;
use App\Http\Clients\PortalResultClient;

beforeEach(function (): void {
    Http::fake([
        'results/course-registration/1' => Http::response([
            'data' => FakeResultClient::RESULTS[0],
            'message' => 'success',
            'status' => true,
        ]),
        'results/department/1/session/2009-2010/level/100' => Http::response([
            'data' => FakeResultClient::RESULTS,
            'message' => 'success',
            'status' => true,
        ]),
        'results/department/1/session/2009-2010/semester/FIRST' => Http::response([
            'data' => FakeResultClient::RESULTS,
            'message' => 'success',
            'status' => true,
        ]),
        'results/registration-number/EBSU-2009-51486' => Http::response([
            'data' => FakeResultClient::RESULTS,
            'message' => 'success',
            'status' => true,
        ]),
        'results/session/2009-2010/course/1' => Http::response([
            'data' => FakeResultClient::RESULTS,
            'message' => 'success',
            'status' => true,
        ]),
    ]);

    $this->client = new PortalResultClient();
});

it('can fetch result from the portal by online course registration id', function (): void {
    $courseRegistrationId = '1';

    $result = $this->client->fetchResultByCourseRegistrationId($courseRegistrationId);

    expect($result)->toBeArray()
        ->and($result['course_registration_id'])->toBe($courseRegistrationId);
});

it('can fetch results from the portal by student registration number', function (): void {
    $registrationNumber = 'EBSU-2009-51486';

    $results = $this->client->fetchResultsByRegistrationNumber($registrationNumber);

    expect($results)->toBeArray()
        ->and($results[0]['registration_number'])->toBe('EBSU/2009/51486');
});

it('can fetch results from the portal by student department session and level', function (): void {
    $departmentId = '1';
    $session = '2009-2010';
    $level = '100';

    $results = $this->client->fetchResultsByDepartmentSessionLevel($departmentId, $session, $level);

    expect($results)->toBeArray()
        ->and($results[0])->toHaveKeys(['id', 'course_registration_id']);
});

it('can fetch results from the portal by student department session and semester', function (): void {
    $departmentId = '1';
    $session = '2009-2010';
    $semester = 'FIRST';

    $results = $this->client->fetchResultsByDepartmentSessionSemester($departmentId, $session, $semester);

    expect($results)->toBeArray()
        ->and($results[0])->toHaveKeys(['id', 'course_registration_id']);
});

it('can fetch results from the portal by student session and course', function (): void {
    $session = '2009-2010';
    $courseId = '3203';

    $results = $this->client->fetchResultsBySessionCourse($session, $courseId);

    expect($results)->toBeArray()->toHaveLength(301)
        ->and($results[0])->toHaveKeys(['id', 'course_registration_id']);
});
