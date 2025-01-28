<?php

declare(strict_types=1);

use App\Enums\ImportEventMethod;
use App\Enums\ImportEventStatus;
use App\Enums\ImportEventType;
use App\Http\Controllers\Download\Departments\DownloadDepartmentsController;
use App\Http\Controllers\Download\Departments\DownloadDepartmentsPageController;
use App\Models\ImportEvent;
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

    assertDatabaseHas(ImportEvent::class,
        [
            'data' => json_encode(['department' => 'all']),
            'method' => ImportEventMethod::ALL,
            'status' => ImportEventStatus::QUEUED,
            'type' => ImportEventType::DEPARTMENTS,
            'user_id' => $user->id,
        ]);

});
