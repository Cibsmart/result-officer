<?php

declare(strict_types=1);

use App\Data\Download\PortalStudentData;
use App\Http\Clients\Fakes\FakeStudentClient;
use App\Services\Api\StudentService;
use Illuminate\Support\Collection;

beforeEach(function (): void {
    $client = new FakeStudentClient();

    $this->service = new StudentService($client);
});

it('can get student by registration number', function (): void {
    $data = $this->service->getStudentByRegistrationNumber('EBSU/2009/51486');

    expect($data)->toBeInstanceOf(PortalStudentData::class)
        ->and($data->registrationNumber)->toBe('EBSU/2009/51486');
});

it('can get students by department and session', function (): void {
    $departmentId = '1';
    $session = '2009/2010';
    $data = $this->service->getStudentsByDepartmentAndSession($departmentId, $session);

    $studentsInDepartmentAndSession = array_filter(
        FakeStudentClient::STUDENTS,
        fn ($student) => $student['department_id'] === $departmentId && $student['entry_session'] === $session,
    );

    expect($data)->toBeInstanceOf(Collection::class)
        ->and($data->count())->toBe(count($studentsInDepartmentAndSession));
});

it('can get students by session', function (): void {
    $session = '2009/2010';
    $data = $this->service->getStudentsBySession($session);

    $studentsInSession = array_filter(
        FakeStudentClient::STUDENTS,
        fn ($student) => $student['entry_session'] === $session,
    );

    expect($data)->toBeInstanceOf(Collection::class)
        ->and($data->count())->toBe(count($studentsInSession));
});
