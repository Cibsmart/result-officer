<?php

declare(strict_types=1);

use App\Enums\Permission;
use App\Enums\Role;
use App\Models\Student;
use Tests\Factories\DepartmentFactory;
use Tests\Factories\ProgramFactory;
use Tests\Factories\StudentFactory;
use Tests\Factories\UserFactory;

use function Pest\Laravel\actingAs;

/**
 * Denial at the HTTP layer, per role.
 *
 * improvements.md §5.5: ten controller tests for seventy-eight controllers, and
 * no negative authorization coverage at all. Every assertion here would have
 * passed with a 200 before Wave D — an authenticated account of any role could
 * reach every route in the application.
 */
function studentFor(): Student
{
    $department = DepartmentFactory::new()->createOne();
    $program = ProgramFactory::new()->createOne(['department_id' => $department->id]);

    return StudentFactory::new()->createOne(['program_id' => $program->id]);
}

it('turns away an account with the user role everywhere', function (string $route): void {
    $user = UserFactory::new()->role(Role::USER)->createOne();

    actingAs($user)->get(route($route))->assertForbidden();
})->with([
    'dashboard',
    'students.index',
    'results.index',
    'search',
    'graduand.index',
    'export.results.page',
    'download.students.page',
    'import.results.index',
    'vettingEvent.index',
]);

it('lets the desk officer read but never write', function (): void {
    $desk = UserFactory::new()->deskOfficer()->createOne();
    $student = studentFor();

    actingAs($desk)->get(route('students.index'))->assertOk();
    actingAs($desk)->get(route('graduand.index'))->assertOk();

    actingAs($desk)->patch(route('student.name.update', $student), [])->assertForbidden();
    actingAs($desk)->patch(route('student.status.update', $student), [])->assertForbidden();
    actingAs($desk)->delete(route('student.destroy', $student))->assertForbidden();
    actingAs($desk)->post(route('students.clearance.store', $student), [])->assertForbidden();
    actingAs($desk)->get(route('import.results.index'))->assertForbidden();
    actingAs($desk)->get(route('download.students.page'))->assertForbidden();
});

it('keeps the exam officer out of transfers, deletions, uploads and curricula', function (): void {
    $exam = UserFactory::new()->examOfficer()->createOne();
    $student = studentFor();

    actingAs($exam)->patch(route('student.registrationNumber.update', $student), [])->assertForbidden();
    actingAs($exam)->patch(route('student.program.update', $student), [])->assertForbidden();
    actingAs($exam)->delete(route('student.destroy', $student))->assertForbidden();
    actingAs($exam)->get(route('import.results.index'))->assertForbidden();
    actingAs($exam)->get(route('import.curriculum.index'))->assertForbidden();
});

it('lets the database officer reach what the exam officer cannot', function (): void {
    $database = UserFactory::new()->databaseOfficer()->createOne();

    actingAs($database)->get(route('import.results.index'))->assertOk();
    actingAs($database)->get(route('import.curriculum.index'))->assertOk();
    actingAs($database)->get(route('download.students.page'))->assertOk();
});

it('reserves user management for the super admin', function (): void {
    expect(Role::ADMIN->grants(Permission::ManageUsers))->toBeFalse()
        ->and(Role::SUPER_ADMIN->grants(Permission::ManageUsers))->toBeTrue();
});

it('refuses an out-of-domain account regardless of its role', function (): void {
    $outsider = UserFactory::new()->admin()->createOne(['email' => 'someone@gmail.com']);

    actingAs($outsider)->get(route('students.index'))->assertForbidden();
});
