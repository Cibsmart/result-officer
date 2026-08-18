<?php

declare(strict_types=1);

namespace App\Enums;

use App\Models\User;

enum SearchGroup: string
{
    case STUDENTS = 'students';
    case COURSES = 'courses';
    case CURRICULA = 'curricula';
    case PROGRAMS = 'programs';
    case DEPARTMENTS = 'departments';
    case FACULTIES = 'faculties';

    /** @return array<int, \App\Enums\SearchGroup> */
    public static function visibleToUser(User $user): array
    {
        return array_values(array_filter(self::cases(), static fn (self $group): bool => $group->isVisibleTo($user)));
    }

    public function label(): string
    {
        return match ($this) {
            self::STUDENTS => 'Students',
            self::COURSES => 'Courses',
            self::CURRICULA => 'Curricula',
            self::PROGRAMS => 'Programs',
            self::DEPARTMENTS => 'Departments',
            self::FACULTIES => 'Faculties',
        };
    }

    /**
     * Every record outside the students group is only reachable through the
     * Filament admin panel, which non-admins cannot open — so those groups are
     * hidden from them rather than offered as dead links.
     */
    public function isVisibleTo(User $user): bool
    {
        return match ($this) {
            self::STUDENTS => true,
            default => $user->isAdmin(),
        };
    }
}
