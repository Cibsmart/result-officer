<?php

declare(strict_types=1);

use App\Actions\Imports\Registrations\ProcessPortalRegistration;
use App\Actions\Imports\Registrations\SavePortalRegistration;
use App\Data\Download\PortalRegistrationData;
use App\Http\Clients\Fakes\FakeRegistrationClient;
use App\Services\Api\RegistrationService;
use Illuminate\Support\Collection;

beforeEach(function (): void {
    $client = new FakeRegistrationClient();

    $this->service = new RegistrationService($client, new SavePortalRegistration(),
        new ProcessPortalRegistration());
});

it('can get course registrations by registration number', function (): void {
    $registrationNumber = 'EBSU/2009/51486';
    $data = $this->service->getRegistrationsByRegistrationNumber($registrationNumber);

    $group = groupArrays(FakeRegistrationClient::REGISTRATIONS, [
        'registration_number' => $registrationNumber,
    ]);

    expect($data)->toBeInstanceOf(Collection::class)
        ->and($data[0])->toBeInstanceOf(PortalRegistrationData::class)
        ->and($data->count())->toBe(count($group));
});

it('can get course registrations by department session and level', function (): void {
    $departmentId = 1;
    $session = '2009/2010';
    $level = 100;

    $data = $this->service->getRegistrationsByDepartmentSessionAndLevel(
        $departmentId,
        $session,
        $level,
    );

    $group = groupArrays(FakeRegistrationClient::REGISTRATIONS, [
        'department_id' => $departmentId,
        'level' => $level,
        'session' => $session,
    ]);

    expect($data)->toBeInstanceOf(Collection::class)
        ->and($data[0])->toBeInstanceOf(PortalRegistrationData::class)
        ->and($data->count())->toBe(count($group));
});

it('can get course registrations by department session and semester', function (): void {
    $departmentId = 1;
    $session = '2009/2010';
    $semester = 'FIRST';

    $data = $this->service->getRegistrationsByDepartmentSessionAndSemester(
        $departmentId,
        $session,
        $semester,
    );

    $group = groupArrays(FakeRegistrationClient::REGISTRATIONS, [
        'department_id' => $departmentId,
        'semester' => $semester,
        'session' => $session,
    ]);

    expect($data)->toBeInstanceOf(Collection::class)
        ->and($data[0])->toBeInstanceOf(PortalRegistrationData::class)
        ->and($data->count())->toBe(count($group));
});

it('can get course registrations by session and course', function (): void {
    $session = '2009/2010';
    $courseId = 1;

    $data = $this->service->getRegistrationsBySessionAndCourse($session, $courseId);

    $group = groupArrays(FakeRegistrationClient::REGISTRATIONS, [
        'course_id' => $courseId,
        'session' => $session,
    ]);

    expect($data)->toBeInstanceOf(Collection::class)
        ->and($data[0])->toBeInstanceOf(PortalRegistrationData::class)
        ->and($data->count())->toBe(count($group));
});
