<?php

declare(strict_types=1);

use App\Actions\Students\ProcessPortalStudent;
use App\Actions\Students\SavePortalStudent;
use App\Data\Download\PortalStudentData;
use App\Enums\ImportEventMethod;
use App\Enums\RawDataStatus;
use App\Http\Clients\Fakes\FakeStudentClient;
use App\Services\Api\StudentService;
use Illuminate\Support\Collection;
use Tests\Factories\DepartmentFactory;
use Tests\Factories\EntryModeFactory;
use Tests\Factories\ImportEventFactory;
use Tests\Factories\LevelFactory;
use Tests\Factories\ProgramFactory;
use Tests\Factories\RawStudentFactory;
use Tests\Factories\SessionFactory;
use Tests\Factories\StateFactory;

use function Pest\Laravel\assertDatabaseCount;
use function Pest\Laravel\assertDatabaseHas;

covers(StudentService::class);

beforeEach(function (): void {
    $client = new FakeStudentClient();

    $this->service = new StudentService($client, new SavePortalStudent(), new ProcessPortalStudent());
});

it('can get students by registration number when the import event method is registration number', function (): void {
    $event = ImportEventFactory::new()->createOne([
        'data' => ['registration_number' => 'EBSU/2009/51486'],
        'method' => ImportEventMethod::REGISTRATION_NUMBER,
    ]);

    $data = $this->service->get($event->method, $event->data);

    expect($data)->toBeInstanceOf(Collection::class)
        ->and($data->first())->toBeInstanceOf(PortalStudentData::class)
        ->and($data->first()->registrationNumber)->toBe('EBSU/2009/51486');
});

it('can get students by department session when the import event method is department session', function (): void {
    $departmentId = 1;
    $session = '2009/2010';

    $event = ImportEventFactory::new()->createOne([
        'data' => ['entry_session' => $session, 'online_department_id' => $departmentId],
        'method' => ImportEventMethod::DEPARTMENT_SESSION,
    ]);

    $data = $this->service->get($event->method, $event->data);

    $group = groupArrays(FakeStudentClient::STUDENTS, [
        'department_id' => $departmentId, 'entry_session' => $session,
    ]);

    expect($data)->toBeInstanceOf(Collection::class)
        ->and($data->first())->toBeInstanceOf(PortalStudentData::class)
        ->and($data->count())->toBe(count($group));
});

it('can get students by session when the import event method is session', function (): void {
    $session = '2009/2010';

    $event = ImportEventFactory::new()->createOne([
        'data' => ['entry_session' => $session],
        'method' => ImportEventMethod::SESSION,
    ]);

    $data = $this->service->get($event->method, $event->data);

    $group = groupArrays(FakeStudentClient::STUDENTS, ['entry_session' => $session]);

    expect($data)->toBeInstanceOf(Collection::class)
        ->and($data->first())->toBeInstanceOf(PortalStudentData::class)
        ->and($data->count())->toBe(count($group));
});

it('throws an exception for invalid method', function (): void {
    $event = ImportEventFactory::new()->createOne([
        'data' => ['registration_number' => 'EBSU/2009/51486'],
        'method' => ImportEventMethod::SESSION_COURSE,
    ]);

    $this->service->get($event->method, $event->data);
})->throws(Exception::class, 'Method session_course not found');

it('returns an empty string when array key does not exist', function (): void {
    $event = ImportEventFactory::new()->createOne([
        'data' => ['registration_number' => 'EBSU/2009/51486'],
        'method' => ImportEventMethod::DEPARTMENT_SESSION,
    ]);

    $data = $this->service->getValue('session', $event->data);

    expect($data)->toBeString()->toBeEmpty();
});

test('the save method iterates over a collection of portal student data and calls the save action', function (): void {
    $session = '2009/2010';

    $event = ImportEventFactory::new()->createOne([
        'data' => ['entry_session' => $session],
        'method' => ImportEventMethod::SESSION,
    ]);

    $data = $this->service->getStudentsBySession($session);

    $this->service->save($event, $data);

    assertDatabaseCount('raw_students', $data->count());
});

test('the process method iterates over a collection of raw students and calls the process action', function (): void {
    $session = '2009/2010';

    $event = ImportEventFactory::new()->createOne([
        'data' => ['entry_session' => $session],
        'method' => ImportEventMethod::SESSION,
    ]);

    $department = DepartmentFactory::new()->createOne(['online_id' => 1]);
    ProgramFactory::new()->createOne(['department_id' => $department->id, 'name' => $department->name]);
    LevelFactory::new()->createOne(['name' => 100]);
    SessionFactory::new()->createOne(['name' => '2009-2010']);
    EntryModeFactory::new()->createOne(['code' => 'UTME']);
    StateFactory::new()->createOne(['name' => 'EBONYI']);
    $data = RawStudentFactory::new()->createOne(['import_event_id' => $event->id, 'department_id' => 1]);

    $this->service->process($event);

    assertDatabaseCount('students', $data->count());

    assertDatabaseHas('raw_students', [
        'import_event_id' => $event->id,
        'status' => RawDataStatus::PROCESSED,
    ]);
});

test('the process method sets status and message for failed processing', function (): void {
    $session = '2009/2010';

    $event = ImportEventFactory::new()->createOne([
        'data' => ['entry_session' => $session],
        'method' => ImportEventMethod::SESSION,
    ]);

    RawStudentFactory::new()->createOne(['import_event_id' => $event->id, 'department_id' => 1]);

    $this->service->process($event);

    assertDatabaseHas('raw_students', [
        'import_event_id' => $event->id,
        'message' => 'No query results for model [App\\Models\\Department].',
        'status' => RawDataStatus::FAILED,
    ]);
});
