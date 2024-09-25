<?php

declare(strict_types=1);

use App\Actions\Students\ProcessPortalStudent;
use App\Actions\Students\SavePortalStudent;
use App\Data\Download\PortalStudentData;
use App\Http\Clients\Fakes\FakeStudentClient;
use App\Services\Api\StudentService;
use Illuminate\Support\Collection;

beforeEach(function (): void {
    $client = new FakeStudentClient();

    $this->service = new StudentService($client, new SavePortalStudent(), new ProcessPortalStudent());
});

it('can get student by registration number', function (): void {
    $data = $this->service->getStudentByRegistrationNumber('EBSU/2009/51486')[0];

    expect($data)->toBeInstanceOf(PortalStudentData::class)
        ->and($data->registrationNumber)->toBe('EBSU/2009/51486');
});

it('can get students by department and session', function (): void {
    $departmentId = 1;
    $session = '2009/2010';
    $data = $this->service->getStudentsByDepartmentAndSession($departmentId, $session);

    $group = groupArrays(FakeStudentClient::STUDENTS, [
        'department_id' => $departmentId, 'entry_session' => $session,
    ]);

    expect($data)->toBeInstanceOf(Collection::class)
        ->and($data->count())->toBe(count($group));
});

it('can get students by session', function (): void {
    $session = '2009/2010';
    $data = $this->service->getStudentsBySession($session);

    $group = groupArrays(FakeStudentClient::STUDENTS, ['entry_session' => $session]);

    expect($data)->toBeInstanceOf(Collection::class)
        ->and($data->count())->toBe(count($group));
});
