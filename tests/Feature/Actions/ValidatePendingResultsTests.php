<?php

declare(strict_types=1);

use App\Actions\Results\ValidatePendingResults;
use App\Models\ResultDetail;
use Tests\Factories\RegistrationFactory;
use Tests\Factories\ResultFactory;
use Tests\Factories\SemesterEnrollmentFactory;

use function Pest\Laravel\assertDatabaseMissing;

it('validates all pending results', function (): void {
    SemesterEnrollmentFactory::new()
        ->has(RegistrationFactory::new()
            ->count(10)
            ->has(ResultFactory::new()))
        ->createOne();

    $action = new ValidatePendingResults();

    $action->execute();

    assertDatabaseMissing(ResultDetail::class, [
        'validate' => true,
    ]);

    $resultDetail = ResultDetail::first();

    expect(Hash::check($resultDetail->value, $resultDetail->data))->toBeTrue();
});
