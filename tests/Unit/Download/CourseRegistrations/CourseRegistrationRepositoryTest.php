<?php

declare(strict_types=1);

use App\Data\Download\PortalCourseRegistrationData;
use App\Http\Clients\Fakes\FakeCourseRegistrationClient;
use App\Http\Clients\Fakes\FakeStudentClient;
use App\Repositories\CourseRegistrationRepository;
use App\Services\Api\CourseRegistrationService;
use Illuminate\Support\Collection;
use Tests\Factories\DepartmentFactory;
use Tests\Factories\EntryModeFactory;
use Tests\Factories\LevelFactory;
use Tests\Factories\ProgramFactory;
use Tests\Factories\SessionFactory;
use Tests\Factories\StateFactory;

use function Pest\Laravel\assertDatabaseCount;
use function Pest\Laravel\withoutExceptionHandling;

beforeEach(function (): void {

    $client = new FakeCourseRegistrationClient();

    $service = new CourseRegistrationService($client);

    $this->repository = new CourseRegistrationRepository($service);
});

it('can get a course registration by registration number', function (): void {
    $data = $this->repository->getCourseRegistrationsByRegistrationNumber('EBSU-2009-51486');

    expect($data)->toBeInstanceOf(Collection::class)
        ->and($data[0])->toBeInstanceOf(PortalCourseRegistrationData::class);
});

it('can get course registrations by department session and level', function (): void {
    $departmentId = '1';
    $session = '2009/2010';
    $level = '100';
    $data = $this->repository->getCourseRegistrationsByDepartmentAndSessionLevel(
        $departmentId, $session, $level,
    );

    expect($data)->toBeInstanceOf(Collection::class)
        ->and($data[0])->toBeInstanceOf(PortalCourseRegistrationData::class);
});

it('can get course registrations by department session and semester', function (): void {
    $departmentId = '1';
    $session = '2009/2010';
    $semester = 'FIRST';
    $data = $this->repository->getCourseRegistrationsByDepartmentSessionAndSemester(
        $departmentId, $session, $semester,
    );

    expect($data)->toBeInstanceOf(Collection::class)
        ->and($data[0])->toBeInstanceOf(PortalCourseRegistrationData::class);
});

it('can get course registrations by session and course', function (): void {
    $session = '2009/2010';
    $courseId = '1';
    $data = $this->repository->getCourseRegistrationsBySessionAndCourse($session, $courseId);

    expect($data)->toBeInstanceOf(Collection::class)
        ->and($data[0])->toBeInstanceOf(PortalCourseRegistrationData::class);
});

it('can save a valid course registration', function (): void {
    withoutExceptionHandling();
    $registration = FakeCourseRegistrationClient::COURSE_REGISTRATIONS[0];
    LevelFactory::new()->create(['name' => $registration['level']]);
    SessionFactory::new()->create(['name' => $registration['session']]);

    $data = $this->repository->getCourseRegistrationsByRegistrationNumber($registration['registration_number']);

    assertDatabaseCount('course_registrations', 0);
    $this->repository->saveCourseRegistrations($data);
    assertDatabaseCount('course_registrations', 1);
});

it('throws exception for invalid registration number', function (): void {
    $student = FakeStudentClient::STUDENTS['invalidRegistrationNumber'];
    LevelFactory::new()->create(['name' => $student['entry_level']]);
    EntryModeFactory::new()->create(['code' => $student['entry_mode']]);
    SessionFactory::new()->create(['name' => $student['entry_session']]);
    $department = DepartmentFactory::new()->createOne(['online_id' => $student['department_id']]);
    ProgramFactory::new()->create(['name' => $department->name, 'department_id' => $department->id]);
    StateFactory::new()->create(['name' => $student['state']]);

    $data = $this->repository->getStudentByRegistrationNumber($student['registration_number']);

    $this->repository->saveStudent($data);
})->throws(InvalidArgumentException::class, 'Invalid registration number');

it('throws exception for invalid gender', function (): void {
    $student = FakeStudentClient::STUDENTS['EBSU-2010-51895'];
    LevelFactory::new()->create(['name' => $student['entry_level']]);
    EntryModeFactory::new()->create(['code' => $student['entry_mode']]);
    SessionFactory::new()->create(['name' => $student['entry_session']]);
    $department = DepartmentFactory::new()->createOne(['online_id' => $student['department_id']]);
    ProgramFactory::new()->create(['name' => $department->name, 'department_id' => $department->id]);
    StateFactory::new()->create(['name' => $student['state']]);

    $data = $this->repository->getStudentByRegistrationNumber($student['registration_number']);

    $this->repository->saveStudent($data);
})->throws(ValueError::class, '"Z" is not a valid backing value for enum App\Enums\GenderEnum');
