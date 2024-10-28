<?php

declare(strict_types=1);

namespace App\Enums;

enum EntryMode: string
{
    case UTME = 'UTME';
    case DENT = 'DENT';
    case PD = 'PD';
    case TRAN = 'TRAN';

    public static function get(string $entryMode): self
    {
        if (in_array($entryMode, [self::DENT->value, 'DE', 'DIRECT-ENTRY', 'DIRECT ENTRY'], true)) {
            return self::DENT;
        }

        if (in_array($entryMode, [self::TRAN->value, 'TRANSFER'], true)) {
            return self::TRAN;
        }

        return self::UTME;
    }
}
