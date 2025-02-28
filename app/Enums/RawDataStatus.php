<?php

declare(strict_types=1);

namespace App\Enums;

enum RawDataStatus: string
{
    case PENDING = 'pending';
    case UPDATING = 'updating';
    case DUPLICATE = 'duplicate';
    case FAILED = 'failed';
    case PROCESSED = 'processed';
}
