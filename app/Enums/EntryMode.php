<?php

declare(strict_types=1);

namespace App\Enums;

enum EntryMode: string
{
    case UTME = 'UTME';
    case DE = 'DE';
    case PD = 'PD';
    case TRAN = 'TRAN';
}
