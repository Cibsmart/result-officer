<?php

declare(strict_types=1);

namespace App\Enums;

enum SortDirection: string
{
    case ASCENDING = 'asc';
    case DESCENDING = 'desc';

    public static function fromValue(?string $value): self
    {
        return self::tryFrom(mb_strtolower((string) $value)) ?? self::ASCENDING;
    }

    public function opposite(): self
    {
        return $this === self::ASCENDING
            ? self::DESCENDING
            : self::ASCENDING;
    }
}
