<?php

declare(strict_types=1);

namespace App\Enums;

enum RecordSource: string
{
    case PORTAL = 'portal';
    case EXCEL = 'excel';
    case LEGACY = 'legacy';
    case SYSTEM = 'system';
}
