<?php

declare(strict_types=1);

use App\Data\Department\DepartmentListData;
use Inertia\Testing\AssertableInertia;
use Tests\Factories\DepartmentFactory;
use Tests\Factories\UserFactory;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;

it('loads the correct component', function (): void {
    $user = UserFactory::new()->createOne();

    actingAs($user)
        ->get(route('department.cleared.index'))
        ->assertInertia(fn (AssertableInertia $inertia) => $inertia
            ->component('reports/cleared/index/page', true));
});

it('redirects guest to login', function (): void {
    $department = DepartmentFactory::new()->createOne();

    get(route('department.cleared.index', $department))->assertRedirect('login');
});

it('passes departments data to the view', function (): void {
    $user = UserFactory::new()->createOne();
    DepartmentFactory::new()->active()->count(3)->create();

    actingAs($user)->get(route('department.cleared.index'))
        ->assertHasDataList('departments', DepartmentListData::new());
});
