<?php

declare(strict_types=1);

use App\Data\Students\StudentBasicData;
use App\Data\Students\StudentIndexFilterData;
use App\Enums\Gender;
use App\Enums\Role;
use App\Enums\StudentStatus;
use App\Models\User;
use App\Queries\StudentIndex;
use Illuminate\Testing\TestResponse;
use Tests\Factories\DepartmentFactory;
use Tests\Factories\LevelFactory;
use Tests\Factories\LocalGovernmentFactory;
use Tests\Factories\ProgramFactory;
use Tests\Factories\SessionFactory;
use Tests\Factories\StudentFactory;
use Tests\Factories\UserFactory;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;

/**
 * The registration numbers of the students on the page, in the order they are listed.
 * @return array<int, string>
 */
function listedRegistrationNumbers(TestResponse $response): array
{
    return array_column($response->viewData('page')['props']['paginated']['data'], 'registrationNumber');
}

function adminUser(): User
{
    return UserFactory::new()->admin()->createOne([
        'email' => fake()->unique()->userName() . '@ebsu.edu.ng',
        'role' => Role::ADMIN,
    ]);
}

it('redirects guest to login', function (): void {
    get(route('students.index'))->assertRedirect('login');
});

it('loads the correct component', function (): void {
    $user = UserFactory::new()->admin()->createOne();

    actingAs($user)->get(route('students.index'))
        ->assertHasComponent('students/index/page');
});

it('passes paginated student data to the view', function (): void {
    $user = UserFactory::new()->admin()->createOne();
    StudentFactory::new()->count(3)->create();

    $students = StudentIndex::new(StudentIndexFilterData::from([
        'search' => '',
        'gender' => null,
        'status' => null,
        'department' => null,
        'entrySession' => null,
        'sort' => 'name',
        'direction' => 'asc',
    ]))->paginate();

    actingAs($user)
        ->get(route('students.index'))
        ->assertHasPaginatedData('paginated', StudentBasicData::collect($students)->withPath(route('students.index')));
});

it('lists students by name ascending by default', function (): void {
    $user = UserFactory::new()->admin()->createOne();

    StudentFactory::new()->createOne(['last_name' => 'Okonkwo', 'registration_number' => 'EBSU/2019/00003']);
    StudentFactory::new()->createOne(['last_name' => 'Adeyemi', 'registration_number' => 'EBSU/2019/00001']);
    StudentFactory::new()->createOne(['last_name' => 'Nwafor', 'registration_number' => 'EBSU/2019/00002']);

    $response = actingAs($user)->get(route('students.index'));

    expect(listedRegistrationNumbers($response))
        ->toBe(['EBSU/2019/00001', 'EBSU/2019/00002', 'EBSU/2019/00003']);
});

it('searches by registration number', function (): void {
    $user = UserFactory::new()->admin()->createOne();

    StudentFactory::new()->createOne(['registration_number' => 'EBSU/2019/12345']);
    StudentFactory::new()->createOne(['registration_number' => 'EBSU/2020/98765']);

    $response = actingAs($user)->get(route('students.index', ['search' => '2019/12345']));

    expect(listedRegistrationNumbers($response))->toBe(['EBSU/2019/12345']);
});

it('searches across every part of a name', function (): void {
    $user = UserFactory::new()->admin()->createOne();

    StudentFactory::new()->createOne([
        'first_name' => 'Chidinma',
        'last_name' => 'Okonkwo',
        'other_names' => 'Ngozi',
        'registration_number' => 'EBSU/2019/00001',
    ]);

    StudentFactory::new()->createOne([
        'first_name' => 'Emeka',
        'last_name' => 'Nwafor',
        'other_names' => 'Chidi',
        'registration_number' => 'EBSU/2019/00002',
    ]);

    $surname = actingAs($user)->get(route('students.index', ['search' => 'okonk']));
    expect(listedRegistrationNumbers($surname))->toBe(['EBSU/2019/00001']);

    $otherName = actingAs($user)->get(route('students.index', ['search' => 'ngozi']));
    expect(listedRegistrationNumbers($otherName))->toBe(['EBSU/2019/00001']);
});

it('matches every token of a multi word search', function (): void {
    $user = UserFactory::new()->admin()->createOne();

    StudentFactory::new()->createOne([
        'first_name' => 'Chidinma',
        'last_name' => 'Okonkwo',
        'registration_number' => 'EBSU/2019/00001',
    ]);

    StudentFactory::new()->createOne([
        'first_name' => 'Emeka',
        'last_name' => 'Okonkwo',
        'registration_number' => 'EBSU/2019/00002',
    ]);

    $response = actingAs($user)->get(route('students.index', ['search' => 'okonkwo chidinma']));

    expect(listedRegistrationNumbers($response))->toBe(['EBSU/2019/00001']);
});

it('filters by gender', function (): void {
    $user = UserFactory::new()->admin()->createOne();

    StudentFactory::new()->createOne(['gender' => Gender::FEMALE, 'registration_number' => 'EBSU/2019/00001']);
    StudentFactory::new()->createOne(['gender' => Gender::MALE, 'registration_number' => 'EBSU/2019/00002']);

    $response = actingAs($user)->get(route('students.index', ['gender' => Gender::FEMALE->value]));

    expect(listedRegistrationNumbers($response))->toBe(['EBSU/2019/00001']);
});

it('filters by status', function (): void {
    $user = UserFactory::new()->admin()->createOne();

    StudentFactory::new()->graduated()->createOne(['registration_number' => 'EBSU/2019/00001']);
    StudentFactory::new()->createOne(['registration_number' => 'EBSU/2019/00002']);

    $response = actingAs($user)->get(route('students.index', ['status' => StudentStatus::GRADUATED->value]));

    expect(listedRegistrationNumbers($response))->toBe(['EBSU/2019/00001']);
});

it('filters by department', function (): void {
    $user = UserFactory::new()->admin()->createOne();

    $department = DepartmentFactory::new()->active()->createOne();
    $program = ProgramFactory::new()->createOne(['department_id' => $department->id]);

    StudentFactory::new()->createOne(['program_id' => $program->id, 'registration_number' => 'EBSU/2019/00001']);
    StudentFactory::new()->createOne(['registration_number' => 'EBSU/2019/00002']);

    $response = actingAs($user)->get(route('students.index', ['department' => $department->id]));

    expect(listedRegistrationNumbers($response))->toBe(['EBSU/2019/00001']);
});

it('filters by entry year', function (): void {
    $user = UserFactory::new()->admin()->createOne();

    $session = SessionFactory::new()->createOne(['name' => '2019/2020', 'slug' => '2019-2020']);

    StudentFactory::new()->createOne(['entry_session_id' => $session->id, 'registration_number' => 'EBSU/2019/00001']);
    StudentFactory::new()->createOne(['registration_number' => 'EBSU/2019/00002']);

    $response = actingAs($user)->get(route('students.index', ['entrySession' => $session->id]));

    expect(listedRegistrationNumbers($response))->toBe(['EBSU/2019/00001']);
});

it('sorts by registration number in either direction', function (): void {
    $user = UserFactory::new()->admin()->createOne();

    StudentFactory::new()->createOne(['registration_number' => 'EBSU/2019/00002']);
    StudentFactory::new()->createOne(['registration_number' => 'EBSU/2019/00001']);

    $ascending = actingAs($user)
        ->get(route('students.index', ['sort' => 'registration_number', 'direction' => 'asc']));

    expect(listedRegistrationNumbers($ascending))->toBe(['EBSU/2019/00001', 'EBSU/2019/00002']);

    $descending = actingAs($user)
        ->get(route('students.index', ['sort' => 'registration_number', 'direction' => 'desc']));

    expect(listedRegistrationNumbers($descending))->toBe(['EBSU/2019/00002', 'EBSU/2019/00001']);
});

it('sorts by department name', function (): void {
    $user = UserFactory::new()->admin()->createOne();

    $zoology = ProgramFactory::new()->createOne([
        'department_id' => DepartmentFactory::new()->active()->createOne(['name' => 'Zoology'])->id,
    ]);

    $botany = ProgramFactory::new()->createOne([
        'department_id' => DepartmentFactory::new()->active()->createOne(['name' => 'Botany'])->id,
    ]);

    StudentFactory::new()->createOne(['program_id' => $zoology->id, 'registration_number' => 'EBSU/2019/00002']);
    StudentFactory::new()->createOne(['program_id' => $botany->id, 'registration_number' => 'EBSU/2019/00001']);

    $response = actingAs($user)->get(route('students.index', ['sort' => 'department', 'direction' => 'asc']));

    expect(listedRegistrationNumbers($response))->toBe(['EBSU/2019/00001', 'EBSU/2019/00002']);
});

it('falls back to the default sort when given an unknown field', function (): void {
    $user = UserFactory::new()->admin()->createOne();

    StudentFactory::new()->createOne(['last_name' => 'Okonkwo', 'registration_number' => 'EBSU/2019/00002']);
    StudentFactory::new()->createOne(['last_name' => 'Adeyemi', 'registration_number' => 'EBSU/2019/00001']);

    $response = actingAs($user)->get(route('students.index', ['sort' => 'drop table', 'direction' => 'sideways']));

    expect(listedRegistrationNumbers($response))->toBe(['EBSU/2019/00001', 'EBSU/2019/00002']);
});

it('keeps the active filters on the pagination links', function (): void {
    $user = UserFactory::new()->admin()->createOne();

    // Two pages' worth of students, all sharing one level/program/session/lga so the
    // factories' unique() pools are not exhausted.
    StudentFactory::new()->count(20)->create([
        'entry_level_id' => LevelFactory::new()->createOne()->id,
        'entry_session_id' => SessionFactory::new()->createOne()->id,
        'gender' => Gender::FEMALE,
        'local_government_id' => LocalGovernmentFactory::new()->createOne()->id,
        'program_id' => ProgramFactory::new()->createOne()->id,
    ]);

    $response = actingAs($user)->get(route('students.index', ['gender' => Gender::FEMALE->value]));

    $next = $response->viewData('page')['props']['paginated']['next_page_url'];

    expect($next)->toContain('gender=F')->toContain('page=2');
});

it('passes the filter state back to the page', function (): void {
    $user = UserFactory::new()->admin()->createOne();

    $response = actingAs($user)->get(route('students.index', [
        'direction' => 'desc',
        'gender' => Gender::MALE->value,
        'search' => 'okonkwo',
        'sort' => 'department',
    ]));

    $filters = $response->viewData('page')['props']['filters'];

    expect($filters['search'])->toBe('okonkwo')
        ->and($filters['gender'])->toBe('M')
        ->and($filters['sort'])->toBe('department')
        ->and($filters['direction'])->toBe('desc')
        ->and($filters['status'])->toBeNull()
        ->and($filters['department'])->toBeNull();
});

it('offers every filter option to an admin', function (): void {
    DepartmentFactory::new()->active()->createOne(['name' => 'Botany']);
    SessionFactory::new()->createOne(['name' => '2019/2020', 'slug' => '2019-2020']);

    $response = actingAs(adminUser())->get(route('students.index'));

    $options = $response->viewData('page')['props']['options'];

    expect(array_column($options['genders'], 'label'))->toBe(['All Genders', 'MALE', 'FEMALE', 'UNKNOWN'])
        ->and(array_column($options['departments'], 'label'))->toBe(['All Departments', 'BOTANY'])
        ->and(array_column($options['years'], 'label'))->toBe(['All Years', '2019'])
        ->and($options['statuses'][0]['label'])->toBe('All Statuses');
});
