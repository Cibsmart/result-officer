<?php

declare(strict_types=1);

use App\Data\Download\PortalStudentData;
use App\Enums\LevelEnum;
use App\Http\Clients\Fakes\FakeStudentClient;
use App\Models\Student;
use App\Repositories\StudentRepository;
use App\Services\Api\StudentService;
use App\Services\ExtractYear;
use Illuminate\Support\Collection;
use Tests\Factories\DepartmentFactory;
use Tests\Factories\EntryModeFactory;
use Tests\Factories\LevelFactory;
use Tests\Factories\ProgramFactory;
use Tests\Factories\SessionFactory;
use Tests\Factories\StateFactory;

use function Pest\Laravel\assertDatabaseCount;

beforeEach(function (): void {

    $client = new FakeStudentClient();

    $service = new StudentService($client);

    $this->repository = new StudentRepository($service);
});

it('can get a student by registration number', function (): void {
    $data = $this->repository->getStudentByRegistrationNumber('EBSU-2009-51486');

    expect($data)->toBeInstanceOf(PortalStudentData::class);
});

it('can get students by department and session', function (): void {
    $departmentId = '1';
    $session = '2009/2010';
    $data = $this->repository->getStudentsByDepartmentAndSession($departmentId, $session);

    expect($data)->toBeInstanceOf(Collection::class);
});

it('can get students by session', function (): void {
    $session = '2009/2010';
    $data = $this->repository->getStudentsBySession($session);

    expect($data)->toBeInstanceOf(Collection::class);
});

it('can save a valid student', function (): void {
    $student = FakeStudentClient::STUDENTS['EBSU-2009-51486'];
    LevelFactory::new()->create(['name' => $student['entry_level']]);
    EntryModeFactory::new()->create(['code' => $student['entry_mode']]);
    SessionFactory::new()->create(['name' => $student['entry_session']]);
    $department = DepartmentFactory::new()->createOne(['online_id' => $student['department_id']]);
    ProgramFactory::new()->create(['name' => $department->name, 'department_id' => $department->id]);
    StateFactory::new()->create(['name' => $student['state']]);

    $data = $this->repository->getStudentByRegistrationNumber($student['registration_number']);

    assertDatabaseCount('students', 0);
    $this->repository->saveStudent($data);
    assertDatabaseCount('students', 1);
});

it('can save valid students', function (): void {
    $session = '2009/2010';
    $student = FakeStudentClient::STUDENTS['EBSU-2009-51486'];
    LevelFactory::new()->create(['name' => $student['entry_level']]);
    EntryModeFactory::new()->create(['code' => $student['entry_mode']]);
    SessionFactory::new()->create(['name' => $session]);
    $department = DepartmentFactory::new()->createOne(['online_id' => $student['department_id']]);
    ProgramFactory::new()->create(['name' => $department->name, 'department_id' => $department->id]);
    StateFactory::new()->create(['name' => $student['state']]);
    StateFactory::new()->create(['name' => 'EBONYI']);

    $data = $this->repository->getStudentsBySession($session);

    assertDatabaseCount('students', 0);

    $this->repository->saveStudents($data);

    $studentsInSession = array_filter(
        FakeStudentClient::STUDENTS,
        fn ($student,
        ) => $student['entry_session'] === $session
            && $student['gender'] !== 'Z'
            && $student['registration_number'] !== 'invalidRegistrationNumber',
    );

    assertDatabaseCount('students', count($studentsInSession));
});

it('can extract session from student registration number', function (): void {
    $student = FakeStudentClient::STUDENTS['EBSU-2009-51487'];
    $session = ExtractYear::fromRegistrationNumber($student['registration_number'])->session();

    LevelFactory::new()->create(['name' => $student['entry_level']]);
    EntryModeFactory::new()->create(['code' => $student['entry_mode']]);
    SessionFactory::new()->create(['name' => '2009/2010']);
    $department = DepartmentFactory::new()->createOne(['online_id' => $student['department_id']]);
    ProgramFactory::new()->create(['name' => $department->name, 'department_id' => $department->id]);
    StateFactory::new()->create(['name' => $student['state']]);

    $data = $this->repository->getStudentByRegistrationNumber($student['registration_number']);

    assertDatabaseCount('students', 0);

    $newStudent = $this->repository->saveStudent($data);

    assertDatabaseCount('students', 1);
    expect($newStudent)->toBeInstanceOf(Student::class)
        ->and($newStudent->entrySession->name)->toBe($session);
});

it('can apply defaults for students without level, entry mode and state', function (): void {
    $defaultLevel = LevelEnum::LEVEL_100;
    $defaultEntryMode = 'UTME';
    $defaultState = 'EBONYI';

    $student = FakeStudentClient::STUDENTS['EBSU-2009-51488'];
    LevelFactory::new()->create(['name' => $defaultLevel]);
    EntryModeFactory::new()->create(['code' => $defaultEntryMode]);
    SessionFactory::new()->create(['name' => $student['entry_session']]);
    $department = DepartmentFactory::new()->createOne(['online_id' => $student['department_id']]);
    ProgramFactory::new()->create(['name' => $department->name, 'department_id' => $department->id]);
    StateFactory::new()->create(['name' => $defaultState]);

    $data = $this->repository->getStudentByRegistrationNumber($student['registration_number']);

    assertDatabaseCount('students', 0);

    $newStudent = $this->repository->saveStudent($data);

    assertDatabaseCount('students', 1);
    expect($newStudent)->toBeInstanceOf(Student::class)
        ->and($newStudent->entryMode->code)->toBe($defaultEntryMode)
        ->and($newStudent->entryLevel->name)->toBe($defaultLevel->value)
        ->and($newStudent->state->name)->toBe($defaultState);
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
