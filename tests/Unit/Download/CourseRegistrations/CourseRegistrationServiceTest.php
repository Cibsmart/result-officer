<?php

declare(strict_types=1);

use App\Data\Download\PortalCourseRegistrationData;
use App\Http\Clients\Fakes\FakeCourseRegistrationClient;
use App\Services\Api\CourseRegistrationService;
use Illuminate\Support\Collection;

beforeEach(function (): void {
    $client = new FakeCourseRegistrationClient();

    $this->service = new CourseRegistrationService($client);
});

it('can get course registrations by registration number', function (): void {
    $data = $this->service->getCourseRegistrationsByRegistrationNumber('EBSU/2009/51486');

    expect($data)->toBeInstanceOf(Collection::class)
        ->and($data[0])->toBeInstanceOf(PortalCourseRegistrationData::class)
        ->and($data->count())->toBe(count(FakeCourseRegistrationClient::COURSE_REGISTRATIONS));
});

it('can get course registrations by department session and level', function (): void {
    $departmentId = '1';
    $session = '2009/2010';
    $level = '100';

    $data = $this->service->getCourseRegistrationsByDepartmentSessionAndLevel(
        $departmentId,
        $session,
        $level,
    );

    $group = groupArrays(FakeCourseRegistrationClient::COURSE_REGISTRATIONS, [
        'department_id' => $departmentId,
        'level' => $level,
        'session' => $session,
    ]);

    expect($data)->toBeInstanceOf(Collection::class)
        ->and($data[0])->toBeInstanceOf(PortalCourseRegistrationData::class)
        ->and($data->count())->toBe(count($group));
});

it('can get course registrations by department session and semester', function (): void {
    $departmentId = '1';
    $session = '2009/2010';
    $semester = 'FIRST';

    $data = $this->service->getCourseRegistrationsByDepartmentSessionAndSemester(
        $departmentId,
        $session,
        $semester,
    );

    $group = groupArrays(FakeCourseRegistrationClient::COURSE_REGISTRATIONS, [
        'department_id' => $departmentId,
        'semester' => $semester,
        'session' => $session,
    ]);

    expect($data)->toBeInstanceOf(Collection::class)
        ->and($data[0])->toBeInstanceOf(PortalCourseRegistrationData::class)
        ->and($data->count())->toBe(count($group));
});

it('can get course registrations by session and course', function (): void {
    $session = '2009/2010';
    $courseId = '1';

    $data = $this->service->getCourseRegistrationsBySessionAndCourse($session, $courseId);

    $group = groupArrays(FakeCourseRegistrationClient::COURSE_REGISTRATIONS, [
        'course_id' => $courseId,
        'session' => $session,
    ]);

    expect($data)->toBeInstanceOf(Collection::class)
        ->and($data[0])->toBeInstanceOf(PortalCourseRegistrationData::class)
        ->and($data->count())->toBe(count($group));
});
