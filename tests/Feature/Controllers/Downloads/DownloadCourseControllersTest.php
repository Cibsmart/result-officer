<?php

declare(strict_types=1);

use App\Enums\ImportEventMethod;
use App\Enums\ImportEventStatus;
use App\Enums\ImportEventType;
use App\Http\Controllers\Download\Courses\DownloadCoursesController;
use App\Http\Controllers\Download\Courses\DownloadCoursesPageController;
use App\Models\ImportEvent;
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

    assertDatabaseHas(ImportEvent::class,
        [
            'data' => json_encode(['course' => 'all']),
            'method' => ImportEventMethod::ALL,
            'status' => ImportEventStatus::QUEUED,
            'type' => ImportEventType::COURSES,
            'user_id' => $user->id,
        ]);
});
