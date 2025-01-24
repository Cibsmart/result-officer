<?php

declare(strict_types=1);

use App\Data\Cleared\ClearedStudentListData;
use App\Data\Department\DepartmentListData;
use Tests\Factories\DepartmentFactory;
use Tests\Factories\ProgramFactory;
use Tests\Factories\StatusChangeEventFactory;
use Tests\Factories\StudentFactory;
use Tests\Factories\UserDepartmentFactory;
use Tests\Factories\UserFactory;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;

it('loads the correct component', function (): void {
    $user = UserFactory::new()->createOne();

    actingAs($user)->get(route('department.cleared.index'))
        ->assertHasComponent('reports/cleared/index/page');
});

it('redirects guest to login', function (): void {
    $department = DepartmentFactory::new()->createOne();

    get(route('department.cleared.index', $department))->assertRedirect('login');
});

it('passes departments data to the view', function (): void {
    $user = UserFactory::new()->has(UserDepartmentFactory::new()->count(2), 'departments')->createOne();

    actingAs($user)->get(route('department.cleared.index'))
        ->assertHasDataList('departments', DepartmentListData::forUser($user));
});

it('passes cleared students data to the view when department and year parameter are present', function (): void {
    $user = UserFactory::new()->createOne();
    $program = ProgramFactory::new()->createOne();
    $department = $program->department;
    $year = now()->year;

    StudentFactory::new()
        ->for($program)
        ->cleared()
        ->count(3)
        ->has(StatusChangeEventFactory::new()->cleared())
        ->create();

    actingAs($user)->get(route('department.cleared.index', [
        'department' => $department,
        'year' => $year,
    ]))
        ->assertHasDataList('students', ClearedStudentListData::fromModel($department, $year));
});

it('fails when supplied invalid department parameter', function (): void {
    $user = UserFactory::new()->createOne();
    $year = now()->year;

    actingAs($user)->get(route('department.cleared.index', [
        'department' => null,
        'year' => $year,
    ]))->assertNotFound();
});

it('fails when supplied invalid year parameter', function (): void {
    $user = UserFactory::new()->createOne();
    $program = ProgramFactory::new()->createOne();
    $department = $program->department;

    actingAs($user)->get(route('department.cleared.index', [
        'department' => $department,
        'year' => 's',
    ]))->assertNotFound();
});
