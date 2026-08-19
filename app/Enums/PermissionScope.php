<?php

declare(strict_types=1);

namespace App\Enums;

/**
 * How far a granted permission reaches.
 *
 * Every grant in Role::permissions() carries one of these. INSTITUTION means the
 * capability applies to any department; DEPARTMENT limits it to the departments
 * assigned to the user through user_departments.
 */
enum PermissionScope: string
{
    case INSTITUTION = 'institution';
    case DEPARTMENT = 'department';

    public function isInstitutionWide(): bool
    {
        return $this === self::INSTITUTION;
    }

    public function label(): string
    {
        return match ($this) {
            self::INSTITUTION => 'Institution-wide',
            self::DEPARTMENT => 'Assigned departments',
        };
    }
}
