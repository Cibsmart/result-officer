<?php

declare(strict_types=1);

use Spatie\LaravelPdf\Facades\Pdf;
use Tests\Factories\FinalStudentFactory;
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
    FinalStudentFactory::new()->for($student)->createOne();

    actingAs($user)
        ->get(route('finalResults.transcript', ['student' => $student]))
        ->assertOk();
});
