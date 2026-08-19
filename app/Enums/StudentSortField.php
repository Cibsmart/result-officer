<?php

declare(strict_types=1);

namespace App\Enums;

enum StudentSortField: string
{
    case NAME = 'name';
    case REGISTRATION_NUMBER = 'registration_number';
    case DEPARTMENT = 'department';

    public static function fromValue(?string $value): self
    {
        return self::tryFrom((string) $value) ?? self::NAME;
    }

    /**
     * The student columns a sort resolves to, in tie-break order. Department sorts
     * on a related table and is therefore handled by the query, not by this list.
     * @return array<int, string>
     */
    public function columns(): array
    {
        return match ($this) {
            self::NAME => ['last_name', 'first_name', 'other_names'],
            self::REGISTRATION_NUMBER => ['registration_number'],
            self::DEPARTMENT => [],
        };
    }
}
