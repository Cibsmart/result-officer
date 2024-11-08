<?php

declare(strict_types=1);

namespace App\Enums;

enum VettingStatus: string
{
    case NEW = 'new';
    case CHECKING = 'checking';
    case FAILED = 'failed';
    case PASSED = 'passed';
    case UNCHECKED = 'unchecked';
}
