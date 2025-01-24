<?php

declare(strict_types=1);

use App\Enums\ImportEventMethod;
use App\Enums\ImportEventStatus;
use App\Enums\ImportEventType;
use App\Http\Controllers\Download\Students\DownloadStudentByRegistrationNumberController;
use App\Http\Controllers\Download\Students\DownloadStudentsByDepartmentSessionController;
use App\Http\Controllers\Download\Students\DownloadStudentsBySessionController;
use App\Http\Controllers\Download\Students\DownloadStudentsPageController;
use App\Models\ImportEvent;
use Illuminate\Foundation\Console\QueuedCommand;
use Tests\Factories\DepartmentFactory;
use Tests\Factories\SessionFactory;
use Tests\Factories\UserFactory;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\withoutExceptionHandling;

covers([
    DownloadStudentsPageController::class,
    DownloadStudentsBySessionController::class,
    DownloadStudentByRegistrationNumberController::class,
    DownloadStudentsByDepartmentSessionController::class,
]);

beforeEach(function (): void {
    Queue::fake();
});

it('renders the download student page', function (): void {
    withoutExceptionHandling();
    $user = UserFactory::new()->createOne();

    $response = actingAs($user)->get(route('download.students.page'));
    $response->assertStatus(200);
});

it('can start download of student by registration number', function (): void {
    $user = UserFactory::new()->createOne();
    $registrationNumber = 'EBSU/2009/51486';

    $response = actingAs($user)
        ->from(route('download.students.page'))
        ->post(route('download.student.registration-number.store'), ['registration_number' => $registrationNumber]);

    $response->assertRedirect(route('download.students.page'));

    assertDatabaseHas('import_events',
        ['user_id' => $user->id, 'data' => json_encode(['registration_number' => $registrationNumber])]);

    $event = ImportEvent::query()->where('user_id', $user->id)->firstOrFail();

    Queue::assertPushed(function (QueuedCommand $command) use ($event): bool {
        $data = getQueuedCommandProtectedDataProperty($command);

        return $data[0] === 'rp:import-portal-data' && $data[1]['eventId'] === $event->id;
    });
});

it('can start download of students by department and session', function (): void {
    $user = UserFactory::new()->createOne();
    $department = DepartmentFactory::new()->createOne(['online_id' => 1]);
    $session = SessionFactory::new()->createOne();

    $response = actingAs($user)
        ->from(route('download.students.page'))
        ->post(route('download.students.department-session.store'), [
            'department' => ['id' => $department->id, 'name' => $department->name],
            'session' => ['id' => $session->id, 'name' => $session->name],
        ]);

    $response->assertRedirect(route('download.students.page'));

    assertDatabaseHas('import_events',
        [
            'data' => json_encode([
                'department' => $department->name,
                'entry_session' => $session->name,
                'online_department_id' => $department->online_id,
            ]),
            'user_id' => $user->id,
        ]);

    $event = ImportEvent::query()->where('user_id', $user->id)->firstOrFail();

    Queue::assertPushed(function (QueuedCommand $command) use ($event): bool {
        $data = getQueuedCommandProtectedDataProperty($command);

        return $data[0] === 'rp:import-portal-data' && $data[1]['eventId'] === $event->id;
    });
});

it('queues download of students by session for processing', function (): void {
    $user = UserFactory::new()->createOne();
    $session = SessionFactory::new()->createOne();

    $response = actingAs($user)
        ->from(route('download.students.page'))
        ->post(route('download.students.session.store'), [
            'session' => ['id' => $session->id, 'name' => $session->name],
        ]);

    $response->assertRedirect(route('download.students.page'));

    assertDatabaseHas('import_events',
        [
            'data' => json_encode(['entry_session' => $session->name]),
            'method' => ImportEventMethod::SESSION,
            'status' => ImportEventStatus::QUEUED,
            'type' => ImportEventType::STUDENTS,
            'user_id' => $user->id,
        ]);
});
