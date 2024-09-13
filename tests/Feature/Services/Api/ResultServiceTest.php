<?php

declare(strict_types=1);

use App\Data\Download\PortalResultData;
use App\Http\Clients\Fakes\FakeResultClient;
use App\Services\Api\ResultService;
use Illuminate\Support\Collection;

beforeEach(function (): void {
    $client = new FakeResultClient();

    $this->service = new ResultService($client);
});

it('can get result by course registration id', function (): void {
    $courseRegistrationId = 1;

    $data = $this->service->getResultByCourseRegistrationId($courseRegistrationId)[0];

    expect($data)->toBeInstanceOf(PortalResultData::class)
        ->and((int) $data->onlineId)->toBe($courseRegistrationId);
});

it('can get a student results by registration numbers', function (): void {
    $registrationNumber = 'EBSU/2009/51486';

    $data = $this->service->getResultsByRegistrationNumber($registrationNumber);

    $group = groupArrays(FakeResultClient::RESULTS, ['registration_number' => $registrationNumber]);

    expect($data)->toBeInstanceOf(Collection::class)
        ->and($data->count())->toBe(count($group));
});

it('can get a student results by department session and level', function (): void {
    $departmentId = 1;
    $session = '2009/2010';
    $level = 100;

    $data = $this->service->getResultsByDepartmentSessionAndLevel($departmentId, $session, $level);

    $group = groupArrays(FakeResultClient::RESULTS, [
        'department_id' => $departmentId, 'level' => $level, 'session' => $session,
    ]);

    expect($data)->toBeInstanceOf(Collection::class)
        ->and($data->count())->toBe(count($group));
});

it('can get a student results by department session and semester', function (): void {
    $departmentId = 1;
    $session = '2009/2010';
    $semester = 'FIRST';

    $data = $this->service->getResultsByDepartmentSessionAndSemester($departmentId, $session, $semester);

    $group = groupArrays(FakeResultClient::RESULTS, [
        'department_id' => $departmentId, 'semester' => $semester, 'session' => $session,
    ]);

    expect($data)->toBeInstanceOf(Collection::class)
        ->and($data->count())->toBe(count($group));
});

it('can get a student results by session and course', function (): void {
    $session = '2009/2010';
    $course = 1;

    $data = $this->service->getResultsBySessionAndCourse($session, $course);

    $group = groupArrays(FakeResultClient::RESULTS, [
        'course_id' => $course, 'session' => $session,
    ]);

    expect($data)->toBeInstanceOf(Collection::class)
        ->and($data->count())->toBe(count($group));
});

it('return empty for non existing registration numbers', function (): void {
    $registrationNumber = 'EBSU/2008/51486';

    $data = $this->service->getResultsByRegistrationNumber($registrationNumber);

    expect($data)->toBeInstanceOf(Collection::class)->isEmpty();
});
