<?php

declare(strict_types=1);

use App\Data\Download\PortalCourseRegistrationData;
use App\Http\Clients\Fakes\FakeCourseRegistrationClient;
use App\Repositories\CourseRegistrationRepository;
use App\Services\Api\CourseRegistrationService;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Support\Collection;
use Tests\Factories\CourseFactory;
use Tests\Factories\LevelFactory;
use Tests\Factories\SemesterFactory;
use Tests\Factories\SessionFactory;
use Tests\Factories\StudentFactory;
use Tests\Factories\YearFactory;

use function Pest\Laravel\assertDatabaseCount;

beforeEach(function (): void {

    $client = new FakeCourseRegistrationClient();

    $service = new CourseRegistrationService($client);

    $this->repository = new CourseRegistrationRepository($service);
});

it('can get a course registration by registration number', function (): void {
    $data = $this->repository->getCourseRegistrationsByRegistrationNumber('EBSU-2009-51486');

    expect($data)->toBeInstanceOf(Collection::class)
        ->and($data[0])->toBeInstanceOf(PortalCourseRegistrationData::class);
});

it('can get course registrations by department session and level', function (): void {
    $departmentId = 1;
    $session = '2009/2010';
    $level = 100;
    $data = $this->repository->getCourseRegistrationsByDepartmentAndSessionLevel(
        $departmentId, $session, $level,
    );

    expect($data)->toBeInstanceOf(Collection::class)
        ->and($data[0])->toBeInstanceOf(PortalCourseRegistrationData::class);
});

it('can get course registrations by department session and semester', function (): void {
    $departmentId = 1;
    $session = '2009/2010';
    $semester = 'FIRST';
    $data = $this->repository->getCourseRegistrationsByDepartmentSessionAndSemester(
        $departmentId, $session, $semester,
    );

    expect($data)->toBeInstanceOf(Collection::class)
        ->and($data[0])->toBeInstanceOf(PortalCourseRegistrationData::class);
});

it('can get course registrations by session and course', function (): void {
    $session = '2009/2010';
    $courseId = 1;
    $data = $this->repository->getCourseRegistrationsBySessionAndCourse($session, $courseId);

    expect($data)->toBeInstanceOf(Collection::class)
        ->and($data[0])->toBeInstanceOf(PortalCourseRegistrationData::class);
});

it('can save a valid course registrations for a student', function (): void {
    $registrationNumber = FakeCourseRegistrationClient::COURSE_REGISTRATIONS[0]['registration_number'];
    LevelFactory::new()->count(2)->sequence(['name' => '100'], ['name' => '200'])->create();
    SessionFactory::new()->count(2)->sequence(['name' => '2009/2010'], ['name' => '2010/2011'])->create();
    StudentFactory::new()->create(['entry_level_id' => 1, 'registration_number' => $registrationNumber]);
    SemesterFactory::new()->count(2)->sequence(['name' => 'FIRST'], ['name' => 'SECOND'])->create();
    CourseFactory::new()->count(4)->sequence(
        fn (Sequence $sequence) => ['online_id' => $sequence->index + 1],
    )->create();
    YearFactory::new()->create();

    $data = $this->repository->getCourseRegistrationsByRegistrationNumber($registrationNumber);

    $expectedRecords = groupArrays(
        FakeCourseRegistrationClient::COURSE_REGISTRATIONS,
        ['registration_number' => $registrationNumber],
    );

    assertDatabaseCount('course_registrations', 0);
    $this->repository->saveCourseRegistrations($data);
    assertDatabaseCount('course_registrations', count($expectedRecords));
});
