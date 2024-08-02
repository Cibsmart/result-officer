<?php

declare(strict_types=1);

namespace App\Enums;

enum StudentStatusEnum: string
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
    case GRADUATED = 'graduated';
}
