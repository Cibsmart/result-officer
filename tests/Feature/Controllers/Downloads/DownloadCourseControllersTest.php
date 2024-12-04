<?php

declare(strict_types=1);

use App\Http\Controllers\Download\Courses\DownloadCoursesController;
use App\Http\Controllers\Download\Courses\DownloadCoursesPageController;
use App\Models\ImportEvent;
use Illuminate\Foundation\Console\QueuedCommand;
use Tests\Factories\UserFactory;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertDatabaseHas;

covers([DownloadCoursesPageController::class, DownloadCoursesController::class]);

beforeEach(function (): void {
    Queue::fake();
});

it('renders the download student page', function (): void {
    $user = UserFactory::new()->createOne();

    $response = actingAs($user)->get(route('download.courses.page'));

    $response->assertStatus(200);
});

it('can start download of all courses', function (): void {
    $user = UserFactory::new()->createOne();

    $response = actingAs($user)
        ->from(route('download.courses.page'))
        ->post(route('download.courses.store'));

    $response->assertRedirect(route('download.courses.page'));

    assertDatabaseHas('import_events',
        ['user_id' => $user->id, 'data' => json_encode(['course' => 'all'])]);

    $event = ImportEvent::query()->where('user_id', $user->id)->firstOrFail();

    Queue::assertPushed(function (QueuedCommand $command) use ($event): bool {
        $data = getQueuedCommandProtectedDataProperty($command);

        return $data[0] === 'rp:import-portal-data' && $data[1]['eventId'] === $event->id;
    });
});
