<?php

declare(strict_types=1);

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum Role: string implements HasLabel
{
    case SUPER_ADMIN = 'super-admin';
    case ADMIN = 'admin';
    case DESK_OFFICER = 'desk-officer';
    case EXAM_OFFICER = 'exam-officer';
    case DATABASE_OFFICER = 'database-officer';
    case USER = 'user';

    /** @return array<string, string> */
    public static function creatable(): array
    {
        return [
            self::ADMIN->value => 'ADMIN',
            self::DATABASE_OFFICER->value => 'DATABASE OFFICER',
            self::DESK_OFFICER->value => 'EXAM OFFICER',
            self::EXAM_OFFICER->value => 'DESK OFFICER',
            self::USER->value => 'USER',
        ];
    }

    /**
     * The capability matrix, as data.
     *
     * Reading is institution-wide for every role that has an assignment at all —
     * scoping a senior role's reads protects nothing while the desk officer
     * already reads every department, and it removes a class of friction around
     * carryover students and joint-taught courses. Only actions are limited to
     * assigned departments.
     *
     * The database officer is a department-scoped administrator: within its
     * departments it does everything an admin does to a student, owns the data
     * pipeline, and sets the curricula students are judged against.
     *
     * The exam officer is deliberately narrower, for two structural reasons
     * rather than seniority. It runs vetting, and a role able to edit a
     * curriculum and then run the check against it could make a failing student
     * pass by lowering the bar. And a programme change can move a student out of
     * the acting officer's own department, so a department-scoped role cannot
     * safely hold it.
     * @return array<string, \App\Enums\PermissionScope>
     */
    public function permissions(): array
    {
        return match ($this) {
            self::SUPER_ADMIN => self::institutionWide(Permission::cases()),
            self::ADMIN => self::institutionWide(array_filter(
                Permission::cases(),
                static fn (Permission $permission): bool => $permission !== Permission::ManageUsers,
            )),
            self::DATABASE_OFFICER => self::databaseOfficerPermissions(),
            self::EXAM_OFFICER => self::examOfficerPermissions(),
            self::DESK_OFFICER => self::institutionWide(Permission::views()),
            self::USER => [],
        };
    }

    public function scopeFor(Permission $permission): ?PermissionScope
    {
        return $this->permissions()[$permission->value] ?? null;
    }

    public function grants(Permission $permission): bool
    {
        return $this->scopeFor($permission) instanceof PermissionScope;
    }

    public function getLabel(): string
    {
        return match ($this) {
            self::SUPER_ADMIN => 'SUPER ADMIN',
            self::ADMIN => 'ADMIN',
            self::DESK_OFFICER => 'DESK OFFICER',
            self::EXAM_OFFICER => 'EXAM OFFICER',
            self::DATABASE_OFFICER => 'DATABASE OFFICER',
            self::USER => 'USER',
        };
    }

    /**
     * A department-scoped administrator: everything an admin does to a student
     * within its own departments, plus the data pipeline and the curricula those
     * students are judged against.
     * @return array<string, \App\Enums\PermissionScope>
     */
    private static function databaseOfficerPermissions(): array
    {
        return [
            ...self::institutionWide(Permission::views()),
            ...self::departmentScoped([
                Permission::AmendStudentDemographics,
                Permission::AmendStudentPlacement,
                Permission::TransferStudent,
                Permission::DeleteStudent,
                Permission::AmendResult,
                Permission::DeleteRegistration,
                Permission::RunPortalDownload,
                Permission::UploadSpreadsheet,
                Permission::ManageImportEvent,
                Permission::RunVetting,
                Permission::ClearStudent,
                Permission::ManageCurriculum,
            ]),
        ];
    }

    /**
     * Academic authority over its own departments. Deliberately without
     * curricula, transfers, deletions and spreadsheet uploads — see the note on
     * permissions() for why each is withheld.
     * @return array<string, \App\Enums\PermissionScope>
     */
    private static function examOfficerPermissions(): array
    {
        return [
            ...self::institutionWide(Permission::views()),
            ...self::departmentScoped([
                Permission::AmendStudentDemographics,
                Permission::AmendStudentPlacement,
                Permission::AmendResult,
                Permission::DeleteRegistration,
                Permission::RunPortalDownload,
                Permission::RunVetting,
                Permission::ClearStudent,
            ]),
        ];
    }

    /**
     * @param iterable<\App\Enums\Permission> $permissions
     * @return array<string, \App\Enums\PermissionScope>
     */
    private static function institutionWide(iterable $permissions): array
    {
        return self::granted($permissions, PermissionScope::INSTITUTION);
    }

    /**
     * @param iterable<\App\Enums\Permission> $permissions
     * @return array<string, \App\Enums\PermissionScope>
     */
    private static function departmentScoped(iterable $permissions): array
    {
        return self::granted($permissions, PermissionScope::DEPARTMENT);
    }

    /**
     * @param iterable<\App\Enums\Permission> $permissions
     * @return array<string, \App\Enums\PermissionScope>
     */
    private static function granted(iterable $permissions, PermissionScope $scope): array
    {
        $granted = [];

        foreach ($permissions as $permission) {
            $granted[$permission->value] = $scope;
        }

        return $granted;
    }
}
