<?php

declare(strict_types=1);

use Tests\Factories\FinalStudentFactory;
use Tests\Factories\RecordsUnitHeadFactory;
use Tests\Factories\UserFactory;

use function Pest\Laravel\actingAs;

test('transcript pdf page loads', function (): void {
    $user = UserFactory::new()->createOne();
    RecordsUnitHeadFactory::new()->active()->createOne();

    $student = createStudentWithResults();
    FinalStudentFactory::new()->for($student)->createOne();

    actingAs($user)
        ->get(route('finalResults.transcript', ['student' => $student]))
        ->assertOk();
});

test('transcript downloads as a pdf', function (): void {
    $user = UserFactory::new()->createOne();
    RecordsUnitHeadFactory::new()->active()->createOne();

    $student = createStudentWithResults();
    FinalStudentFactory::new()->for($student)->createOne();

    $response = actingAs($user)
        ->get(route('finalResults.download', ['student' => $student]))
        ->assertOk()
        ->assertHeader('content-type', 'application/pdf');

    expect($response->headers->get('content-disposition'))
        ->toContain("{$student->registration_number}-results.pdf")
        ->and($response->getContent())->toStartWith('%PDF-');
});
