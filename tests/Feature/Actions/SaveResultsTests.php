<?php

declare(strict_types=1);

use App\Actions\SaveResults;
use App\Data\Download\PortalResultData;
use App\Http\Clients\Fakes\FakeResultClient;
use Illuminate\Database\Eloquent\Factories\Sequence;
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
    $saveAction->execute($resultData);
    assertDatabaseCount('results', $numberOfResults);

});
