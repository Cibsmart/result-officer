<?php

declare(strict_types=1);

namespace App\Enums;

/**
 * The capability vocabulary. Every route carries exactly one of these, and a
 * role holds a set of them at a given PermissionScope.
 *
 * Before this existed the only runtime checks were isAdmin() and isSuperAdmin(),
 * so four of the six roles — desk officer, exam officer, database officer and
 * user — were indistinguishable, and 105 of 110 routes sat behind a bare `auth`
 * middleware that any authenticated account satisfied.
 *
 * Cases are grouped by whether they read or write, because that distinction
 * decides scope: reading is institution-wide for every role that has an
 * assignment at all, and only actions are limited to assigned departments.
 */
enum Permission: string
{
    // Views — reading, printing, exporting, reporting.
    case ViewStudents = 'student.view';
    case ViewResults = 'result.view';
    case ExportResults = 'result.export';
    case ViewVetting = 'vetting.view';
    case ViewGraduands = 'graduand.view';
    case ViewTranscript = 'transcript.view';

    // Actions — student records.
    case AmendStudentDemographics = 'student.amend-demographics';
    case AmendStudentPlacement = 'student.amend-placement';
    case TransferStudent = 'student.transfer';
    case DeleteStudent = 'student.delete';

    // Actions — results.
    case AmendResult = 'result.amend';
    case DeleteRegistration = 'registration.delete';

    // Actions — data pipeline.
    case RunPortalDownload = 'import.portal-download';
    case UploadSpreadsheet = 'import.upload-spreadsheet';
    case ManageImportEvent = 'import.manage-event';

    // Actions — graduation.
    case RunVetting = 'vetting.run';
    case ClearStudent = 'student.clear';

    // Administration.
    case ManageReferenceData = 'admin.reference-data';
    case ManageCurriculum = 'admin.curriculum';
    case ManageUsers = 'admin.users';

    /** @return list<self> */
    public static function views(): array
    {
        return [
            self::ViewStudents,
            self::ViewResults,
            self::ExportResults,
            self::ViewVetting,
            self::ViewGraduands,
            self::ViewTranscript,
        ];
    }

    public function isView(): bool
    {
        return in_array($this, self::views(), true);
    }

    /**
     * Changing a curriculum silently changes who can graduate, and the identity
     * and deletion actions are irreversible. These carry a mandatory remark and
     * DBMail memo — see docs/plans/audit-trail.md decision 2.
     */
    public function requiresMemo(): bool
    {
        return match ($this) {
            self::TransferStudent,
            self::DeleteStudent,
            self::AmendResult,
            self::DeleteRegistration,
            self::ManageCurriculum => true,
            default => false,
        };
    }

    public function label(): string
    {
        return match ($this) {
            self::ViewStudents => 'View students and search',
            self::ViewResults => 'View, print and summarise results',
            self::ExportResults => 'Export result sheets',
            self::ViewVetting => 'View vetting outcomes',
            self::ViewGraduands => 'View graduands and cleared reports',
            self::ViewTranscript => 'Issue a transcript',
            self::AmendStudentDemographics => 'Amend demographic fields',
            self::AmendStudentPlacement => 'Amend academic placement',
            self::TransferStudent => 'Change registration number or programme',
            self::DeleteStudent => 'Delete a student',
            self::AmendResult => 'Amend a result',
            self::DeleteRegistration => 'Delete a registration',
            self::RunPortalDownload => 'Run portal downloads',
            self::UploadSpreadsheet => 'Upload spreadsheets',
            self::ManageImportEvent => 'Cancel, resume or delete an import',
            self::RunVetting => 'Run vetting',
            self::ClearStudent => 'Clear a student for graduation',
            self::ManageReferenceData => 'Manage reference data',
            self::ManageCurriculum => 'Manage curricula and credit units',
            self::ManageUsers => 'Manage users, roles and departments',
        };
    }
}
