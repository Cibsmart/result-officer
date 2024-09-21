<?php

declare(strict_types=1);

use Illuminate\Foundation\Console\QueuedCommand;
use Tests\Factories\UserFactory;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertDatabaseHas;

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

    Queue::assertPushed(QueuedCommand::class);
});
