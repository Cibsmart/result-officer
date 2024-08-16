<?php

declare(strict_types=1);

use Tests\Factories\UserFactory;

use function Pest\Laravel\actingAs;

it('renders the download result page', function (): void {
    $user = UserFactory::new()->createOne();

    $response = actingAs($user)->get(route('download.results.page'));

    $response->assertStatus(200);
});
