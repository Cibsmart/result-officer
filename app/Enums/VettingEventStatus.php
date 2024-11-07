<?php

declare(strict_types=1);

namespace App\Enums;

enum VettingEventStatus: string
{
    case NEW = 'new';
    case PROCESSING = 'processing';
    case CANCELLED = 'cancelled';
    case FAILED = 'failed';
    case COMPLETED = 'completed';
}
