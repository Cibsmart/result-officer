<?php

declare(strict_types=1);

namespace App\Enums;

enum ImportStatus: string
{
    case NEW = 'new';
    case STARTED = 'started';
    case PROCESSING = 'processing';
    case FAILED = 'failed';
    case COMPLETED = 'completed';
}
