<?php

declare(strict_types=1);

namespace App\Enums;

enum RawDataStatus: string
{
    case PENDING = 'pending';
    case PROCESSED = 'processed';
}
