<?php

declare(strict_types=1);

use Spatie\LaravelPdf\Facades\Pdf;
use Tests\Factories\RecordsUnitHeadFactory;
use Tests\Factories\UserFactory;

use function Pest\Laravel\actingAs;

beforeEach(function (): void {
    Pdf::fake();
});

test('transcript pdf page loads', function (): void {
    $user = UserFactory::new()->createOne();
    RecordsUnitHeadFactory::new()->active()->createOne();

    $student = createStudentWithResults();

    actingAs($user)
        ->get(route('results.transcript', ['student' => $student->id]))
        ->assertOk();
});
