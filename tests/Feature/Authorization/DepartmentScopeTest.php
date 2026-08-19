<?php

declare(strict_types=1);

use App\Models\ImportEvent;
use Tests\Factories\DepartmentFactory;
use Tests\Factories\SessionFactory;
use Tests\Factories\UserFactory;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertDatabaseCount;

/**
 * Department scoping used to be presentational only. DepartmentListData::forUser()
 * narrowed the dropdown for non-admins, but the server validated the submitted
 * department id with `exists:departments,id` and nothing else — so an officer
 * scoped to one department could edit one form field and pull another
 * department's records.
 *
 * These assert denial, which is the case the suite did not previously cover.
 */
it('refuses a download for a department the user is not assigned to', function (): void {
    $assigned = DepartmentFactory::new()->createOne(['online_id' => 1]);
    $other = DepartmentFactory::new()->createOne(['online_id' => 2]);
    $session = SessionFactory::new()->createOne();

    $user = UserFactory::new()->forDepartment($assigned)->createOne();

    actingAs($user)
        ->from(route('download.students.page'))
        ->post(route('download.students.department-session.store'), [
            'department' => ['id' => $other->id, 'name' => $other->name],
            'session' => ['id' => $session->id, 'name' => $session->name],
        ])
        ->assertSessionHasErrors('department.id');

    assertDatabaseCount(ImportEvent::class, 0);
});

it('refuses a download for a user assigned to no department at all', function (): void {
    $department = DepartmentFactory::new()->createOne(['online_id' => 1]);
    $session = SessionFactory::new()->createOne();

    $user = UserFactory::new()->createOne();

    actingAs($user)
        ->from(route('download.students.page'))
        ->post(route('download.students.department-session.store'), [
            'department' => ['id' => $department->id, 'name' => $department->name],
            'session' => ['id' => $session->id, 'name' => $session->name],
        ])
        ->assertSessionHasErrors('department.id');

    assertDatabaseCount(ImportEvent::class, 0);
});

it('allows a download for the department the user is assigned to', function (): void {
    $department = DepartmentFactory::new()->createOne(['online_id' => 1]);
    $session = SessionFactory::new()->createOne();

    $user = UserFactory::new()->forDepartment($department)->createOne();

    actingAs($user)
        ->from(route('download.students.page'))
        ->post(route('download.students.department-session.store'), [
            'department' => ['id' => $department->id, 'name' => $department->name],
            'session' => ['id' => $session->id, 'name' => $session->name],
        ])
        ->assertSessionHasNoErrors();

    assertDatabaseCount(ImportEvent::class, 1);
});

it('lets an in-domain administrator reach any department', function (): void {
    $department = DepartmentFactory::new()->createOne(['online_id' => 1]);
    $session = SessionFactory::new()->createOne();

    $admin = UserFactory::new()->createOne([
        'email' => 'registrar@' . config('rp.domain'),
        'role' => 'admin',
    ]);

    actingAs($admin)
        ->from(route('download.students.page'))
        ->post(route('download.students.department-session.store'), [
            'department' => ['id' => $department->id, 'name' => $department->name],
            'session' => ['id' => $session->id, 'name' => $session->name],
        ])
        ->assertSessionHasNoErrors();

    assertDatabaseCount(ImportEvent::class, 1);
});
