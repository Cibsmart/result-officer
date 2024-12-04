<?php

declare(strict_types=1);

use App\Http\Controllers\Download\Departments\DownloadDepartmentsController;
use App\Http\Controllers\Download\Departments\DownloadDepartmentsPageController;
use App\Models\ImportEvent;
use Illuminate\Foundation\Console\QueuedCommand;
use Tests\Factories\UserFactory;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertDatabaseHas;

covers([DownloadDepartmentsPageController::class, DownloadDepartmentsController::class]);

beforeEach(function (): void {
    Queue::fake();
});

it('renders the download student page', function (): void {
    $user = UserFactory::new()->createOne();

    $response = actingAs($user)->get(route('download.departments.page'));

    $response->assertStatus(200);
});

it('can start download of all departments', function (): void {
    $user = UserFactory::new()->createOne();

    $response = actingAs($user)
        ->from(route('download.departments.page'))
        ->post(route('download.departments.store'));

    $response->assertRedirect(route('download.departments.page'));

    assertDatabaseHas('import_events',
        ['user_id' => $user->id, 'data' => json_encode(['department' => 'all'])]);

    $event = ImportEvent::query()->where('user_id', $user->id)->firstOrFail();

    Queue::assertPushed(function (QueuedCommand $command) use ($event): bool {
        $data = getQueuedCommandProtectedDataProperty($command);

        return $data[0] === 'rp:import-portal-data' && $data[1]['eventId'] === $event->id;
    });
});
