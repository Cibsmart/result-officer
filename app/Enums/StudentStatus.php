<?php

declare(strict_types=1);

namespace App\Enums;

enum StudentStatus: string
{
    case NEW = 'new';
    case ACTIVE = 'active';
    case INACTIVE = 'inactive';
    case PROBATION = 'probation';
    case WITHDRAWN = 'withdrawn';
    case EXPELLED = 'expelled';
    case SUSPENDED = 'suspended';
    case DECEASED = 'deceased';
    case TRANSFERRED = 'transferred';
    case FINAL_YEAR = 'final';
    case CHECKED = 'checked';
    case CLEARED = 'cleared';
    case GRADUATED = 'graduated';
}
