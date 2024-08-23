<?php

declare(strict_types=1);

use App\Data\Download\PortalResultData;
use App\Data\Response\ResponseData;
use App\Http\Clients\Fakes\FakeResultClient;
use App\Repositories\ResultRepository;
use App\Services\Api\ResultService;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Support\Collection;
use Tests\Factories\CourseRegistrationFactory;
use Tests\Factories\SemesterEnrollmentFactory;

use function Pest\Laravel\assertDatabaseCount;
use function Pest\Laravel\assertDatabaseEmpty;

beforeEach(function (): void {

    $client = new FakeResultClient();

    $service = new ResultService($client);

    $this->repository = new ResultRepository($service);
});

it('can get result by course registration id', function (): void {
    $courseRegistrationId = '1';

    $data = $this->repository->getResultByCourseRegistrationId($courseRegistrationId);

    expect($data)->toBeInstanceOf(PortalResultData::class)
        ->and($data->courseRegistrationId)->toBe($courseRegistrationId);
});

it('can get results by registration number', function (): void {
    $registrationNumber = 'EBSU/2009/51486';

    $data = $this->repository->getResultByRegistrationNumber($registrationNumber);

    expect($data)->toBeInstanceOf(Collection::class)
        ->and($data->first()->registrationNumber)->toBe($registrationNumber);
});

it('can get results by department session and level', function (): void {
    $departmentId = '1';
    $session = '2009/2010';
    $level = '100';

    $data = $this->repository->getResultByDepartmentSessionAndLevel($departmentId, $session, $level);

    expect($data)->toBeInstanceOf(Collection::class)
        ->and($data->first())->toBeInstanceOf(PortalResultData::class);
});

it('can get results by department session and semester', function (): void {
    $departmentId = '1';
    $session = '2009/2010';
    $semester = 'FIRST';

    $data = $this->repository->getResultByDepartmentSessionAndSemester($departmentId, $session, $semester);

    expect($data)->toBeInstanceOf(Collection::class)
        ->and($data->first())->toBeInstanceOf(PortalResultData::class);
});

it('can get results by session and course', function (): void {
    $session = '2009/2010';
    $course = '1';

    $data = $this->repository->getResultBySessionAndCourse($session, $course);

    expect($data)->toBeInstanceOf(Collection::class)
        ->and($data->first())->toBeInstanceOf(PortalResultData::class);
});

it('can save a valid result for a course registration', function (): void {
    $registration = CourseRegistrationFactory::new()->createOne(['online_id' => 1]);

    $result = FakeResultClient::RESULTS[0];

    $data = PortalResultData::from($result);

    assertDatabaseEmpty('results');
    $this->repository->saveResult($registration, $data);
    assertDatabaseCount('results', 1);
});

it('throws exception for non-existent course registration', function (): void {

    $result = FakeResultClient::RESULTS[0];

    $data = PortalResultData::from($result);

    $this->repository->saveResult(null, $data);
})->throws(Exception::class, 'COURSE REGISTRATION NOT FOUND: Download course registration records and try again');

it('can save results for course registration', function (): void {
    $semesterEnrollment = SemesterEnrollmentFactory::new()->createOne();
    $numberOfResults = 6;

    CourseRegistrationFactory::new()
        ->count($numberOfResults)
        ->sequence(fn (Sequence $sequence) => ['online_id' => $sequence->index + 1])
        ->create(['semester_enrollment_id' => $semesterEnrollment->id]);

    $results = array_slice(FakeResultClient::RESULTS, 0, $numberOfResults);

    $resultData = PortalResultData::collect(collect($results));

    assertDatabaseEmpty('results');

    $responses = $this->repository->saveResults($resultData);

    expect($responses)->each(fn ($response) => $response->message->toBeTrue())
        ->and($responses)->toBeInstanceOf(Collection::class)
        ->and($responses->first())->toBeInstanceOf(ResponseData::class);

    assertDatabaseCount('results', $numberOfResults);
});
