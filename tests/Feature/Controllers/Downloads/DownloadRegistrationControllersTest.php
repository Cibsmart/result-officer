<?php

declare(strict_types=1);

use App\Enums\ImportEventMethod;
use App\Http\Controllers\Download\Registrations\DownloadRegistrationPageController;
use App\Http\Controllers\Download\Registrations\DownloadRegistrationsByDepartmentSessionLevelController;
use App\Http\Controllers\Download\Registrations\DownloadRegistrationsByDepartmentSessionSemesterController;
use App\Http\Controllers\Download\Registrations\DownloadRegistrationsByRegistrationNumberController;
use App\Http\Controllers\Download\Registrations\DownloadRegistrationsBySessionCourseController;
use App\Models\ImportEvent;
use Illuminate\Foundation\Console\QueuedCommand;
use Tests\Factories\CourseFactory;
use Tests\Factories\DepartmentFactory;
use Tests\Factories\LevelFactory;
use Tests\Factories\SemesterFactory;
use Tests\Factories\SessionFactory;
use Tests\Factories\StudentFactory;
use Tests\Factories\UserFactory;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertDatabaseHas;

covers([
    DownloadRegistrationPageController::class,
    DownloadRegistrationsBySessionCourseController::class,
    DownloadRegistrationsByRegistrationNumberController::class,
    DownloadRegistrationsByDepartmentSessionLevelController::class,
    DownloadRegistrationsByDepartmentSessionSemesterController::class,
]);

beforeEach(function (): void {
    Queue::fake();
});

it('renders the download course registration page', function (): void {
    $user = UserFactory::new()->createOne();

    $response = actingAs($user)->get(route('download.registrations.page'));

    $response->assertStatus(200);
});

it('can start download of registrations by registration number', function (): void {
    $user = UserFactory::new()->createOne();
    $student = StudentFactory::new()->createOne(['registration_number' => 'EBSU/2009/51486']);

    $response = actingAs($user)
        ->from(route('download.registrations.page'))
        ->post(route('download.registrations.registration-number.store'),
            ['registration_number' => $student->registration_number]);

    $response->assertRedirect(route('download.registrations.page'));

    assertDatabaseHas('import_events',
        [
            'data' => json_encode(['registration_number' => $student->registration_number]),
            'method' => ImportEventMethod::REGISTRATION_NUMBER->value,
            'user_id' => $user->id,
        ]);

    Queue::assertPushed(QueuedCommand::class);
});

it('can start download of registrations by department, session and level', function (): void {
    $user = UserFactory::new()->createOne();
    $department = DepartmentFactory::new()->createOne(['online_id' => 1]);
    $session = SessionFactory::new()->createOne();
    $level = LevelFactory::new()->createOne();

    $response = actingAs($user)
        ->from(route('download.registrations.page'))
        ->post(route('download.registrations.department-session-level.store'), [
            'department' => ['id' => $department->id, 'name' => $department->name],
            'level' => ['id' => $level->id, 'name' => $level->name],
            'session' => ['id' => $session->id, 'name' => $session->name],
        ]);

    $response->assertRedirect(route('download.registrations.page'));

    assertDatabaseHas('import_events',
        [
            'data' => json_encode([
                'department' => $department->name,
                'level' => (int) $level->name,
                'online_department_id' => $department->online_id,
                'session' => $session->name,
            ]),
            'method' => ImportEventMethod::DEPARTMENT_SESSION_LEVEL->value,
            'user_id' => $user->id,
        ]);

    Queue::assertPushed(QueuedCommand::class);
});

it('can start download of registrations by department, session and semester', function (): void {
    $user = UserFactory::new()->createOne();
    $department = DepartmentFactory::new()->createOne(['online_id' => 1]);
    $session = SessionFactory::new()->createOne();
    $semester = SemesterFactory::new()->createOne();

    $response = actingAs($user)
        ->from(route('download.registrations.page'))
        ->post(route('download.registrations.department-session-semester.store'), [
            'department' => ['id' => $department->id, 'name' => $department->name],
            'semester' => ['id' => $semester->id, 'name' => $semester->name],
            'session' => ['id' => $session->id, 'name' => $session->name],
        ]);

    $response->assertRedirect(route('download.registrations.page'));

    assertDatabaseHas('import_events',
        [
            'data' => json_encode([
                'department' => $department->name,
                'online_department_id' => $department->online_id,
                'semester' => $semester->name,
                'session' => $session->name,
            ]),
            'method' => ImportEventMethod::DEPARTMENT_SESSION_SEMESTER->value,
            'user_id' => $user->id,
        ]);

    Queue::assertPushed(QueuedCommand::class);
});

it('can start download of registrations by session and course', function (): void {
    $user = UserFactory::new()->createOne();
    $session = SessionFactory::new()->createOne();
    $course = CourseFactory::new()->createOne(['online_id' => 1]);

    $response = actingAs($user)
        ->from(route('download.registrations.page'))
        ->post(route('download.registrations.session-course.store'), [
            'course' => ['id' => $course->id, 'name' => $course->name],
            'session' => ['id' => $session->id, 'name' => $session->name],
        ]);

    $response->assertRedirect(route('download.registrations.page'));

    assertDatabaseHas('import_events',
        [
            'data' => json_encode([
                'course' => $course->name,
                'online_course_id' => $course->online_id,
                'session' => $session->name,
            ]),
            'method' => ImportEventMethod::SESSION_COURSE->value,
            'user_id' => $user->id,
        ]);

    Queue::assertPushed(QueuedCommand::class);
});
