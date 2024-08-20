<?php

declare(strict_types=1);

use App\Actions\SaveResults;
use App\Data\Download\PortalResultData;
use App\Data\Response\ResponseData;
use App\Http\Clients\Fakes\FakeResultClient;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Support\Collection;
use Tests\Factories\CourseRegistrationFactory;
use Tests\Factories\SemesterEnrollmentFactory;

use function Pest\Laravel\assertDatabaseCount;
use function Pest\Laravel\assertDatabaseEmpty;

it('can save results', function (): void {
    $semesterEnrollment = SemesterEnrollmentFactory::new()->createOne();
    $numberOfResults = 6;

    CourseRegistrationFactory::new()
        ->count($numberOfResults)
        ->sequence(fn (Sequence $sequence) => ['online_id' => $sequence->index + 1])
        ->create(['semester_enrollment_id' => $semesterEnrollment->id]);

    $results = array_slice(FakeResultClient::RESULTS, 0, $numberOfResults);

    $resultData = PortalResultData::collect(collect($results));

    $saveAction = new SaveResults();

    assertDatabaseEmpty('results');

    $responses = $saveAction->execute($resultData);

    expect($responses)->each(fn ($response) => $response->message->toBeTrue())
        ->and($responses)->toBeInstanceOf(Collection::class)
        ->and($responses->first())->toBeInstanceOf(ResponseData::class);

    assertDatabaseCount('results', $numberOfResults);
});
