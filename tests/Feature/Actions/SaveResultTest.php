<?php

declare(strict_types=1);

use App\Actions\SaveResult;
use App\Data\Download\PortalResultData;
use App\Http\Clients\Fakes\FakeResultClient;
use Tests\Factories\CourseRegistrationFactory;

use function Pest\Laravel\assertDatabaseCount;
use function Pest\Laravel\assertDatabaseEmpty;

it('saves a result', function (): void {
    $registration = CourseRegistrationFactory::new()->createOne(['online_id' => 1]);

    $result = FakeResultClient::RESULTS[0];

    $data = PortalResultData::from($result);

    $action = new SaveResult();

    assertDatabaseEmpty('results');
    $action->execute($registration, $data);
    assertDatabaseCount('results', 1);
});

it('throws exception for non-existent course registration', function (): void {

    $result = FakeResultClient::RESULTS[0];

    $data = PortalResultData::from($result);

    $action = new SaveResult();

    $action->execute(null, $data);
})->throws(Exception::class, 'COURSE REGISTRATION NOT FOUND: Download course registration records and try again');
