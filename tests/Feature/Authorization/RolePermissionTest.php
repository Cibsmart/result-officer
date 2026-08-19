<?php

declare(strict_types=1);

use App\Enums\Permission;
use App\Enums\PermissionScope;
use App\Enums\Role;

/**
 * The agreed capability matrix, restated independently of Role::permissions().
 *
 * Duplication is the point: changing the enum without changing this table fails
 * the build, so a permission cannot quietly widen. The table below is the one
 * signed off for Wave D — see docs/plans/roadmap.md.
 */
/** @return list<string> */
function agreedViews(): array
{
    return ['student.view', 'result.view', 'result.export', 'vetting.view', 'graduand.view', 'transcript.view'];
}

/** @return list<string> */
function everyPermission(): array
{
    return array_map(static fn (Permission $p): string => $p->value, Permission::cases());
}

/** @return array<string, array{institution: list<string>, department: list<string>}> */
function expectedMatrix(): array
{
    return [
        Role::ADMIN->value => [
            'department' => [],
            'institution' => array_values(array_diff(everyPermission(), ['admin.users'])),
        ],
        Role::DATABASE_OFFICER->value => [
            'department' => [
                'student.amend-demographics', 'student.amend-placement',
                'student.transfer', 'student.delete',
                'result.amend', 'registration.delete',
                'import.portal-download', 'import.upload-spreadsheet', 'import.manage-event',
                'vetting.run', 'student.clear', 'admin.curriculum',
            ],
            'institution' => agreedViews(),
        ],
        Role::DESK_OFFICER->value => ['institution' => agreedViews(), 'department' => []],
        Role::EXAM_OFFICER->value => [
            'department' => [
                'student.amend-demographics', 'student.amend-placement',
                'result.amend', 'registration.delete',
                'import.portal-download', 'vetting.run', 'student.clear',
            ],
            'institution' => agreedViews(),
        ],
        Role::SUPER_ADMIN->value => ['institution' => everyPermission(), 'department' => []],
        Role::USER->value => ['institution' => [], 'department' => []],
    ];
}

it('grants exactly the agreed permissions at the agreed scope', function (Role $role): void {
    $expected = expectedMatrix()[$role->value];
    $granted = $role->permissions();

    $institution = array_keys(array_filter(
        $granted,
        static fn (PermissionScope $scope): bool => $scope === PermissionScope::INSTITUTION,
    ));
    $department = array_keys(array_filter(
        $granted,
        static fn (PermissionScope $scope): bool => $scope === PermissionScope::DEPARTMENT,
    ));

    sort($institution);
    sort($department);
    sort($expected['institution']);
    sort($expected['department']);

    expect($institution)->toBe($expected['institution'])
        ->and($department)->toBe($expected['department']);
})->with(fn () => Role::cases());

it('gives the user role nothing at all', function (): void {
    expect(Role::USER->permissions())->toBe([]);

    foreach (Permission::cases() as $permission) {
        expect(Role::USER->grants($permission))->toBeFalse();
    }
});

it('lets only the super admin manage users', function (): void {
    foreach (Role::cases() as $role) {
        expect($role->grants(Permission::ManageUsers))
            ->toBe($role === Role::SUPER_ADMIN, "{$role->value} and admin.users");
    }
});

it(
    'keeps the exam officer out of curricula, transfers, deletions and uploads',
    function (Permission $permission): void {
        expect(Role::EXAM_OFFICER->grants($permission))->toBeFalse()
            ->and(Role::DATABASE_OFFICER->grants($permission))->toBeTrue();
    },
)->with([
    'curricula' => Permission::ManageCurriculum,
    'deletions' => Permission::DeleteStudent,
    'import control' => Permission::ManageImportEvent,
    'spreadsheet uploads' => Permission::UploadSpreadsheet,
    'transfers' => Permission::TransferStudent,
]);

it('reads institution-wide for every role that reads at all', function (Role $role): void {
    foreach (Permission::views() as $view) {
        $scope = $role->scopeFor($view);

        if ($scope === null) {
            expect($role)->toBe(Role::USER);

            continue;
        }

        expect($scope)->toBe(PermissionScope::INSTITUTION, "{$role->value} reads {$view->value}");
    }
})->with(fn () => Role::cases());

it('scopes every action a non-administrator holds to their departments', function (Role $role): void {
    foreach ($role->permissions() as $value => $scope) {
        $permission = Permission::from($value);

        if ($permission->isView()) {
            continue;
        }

        expect($scope)->toBe(PermissionScope::DEPARTMENT, "{$role->value} acts on {$value}");
    }
})->with([
    'database officer' => Role::DATABASE_OFFICER,
    'exam officer' => Role::EXAM_OFFICER,
]);
