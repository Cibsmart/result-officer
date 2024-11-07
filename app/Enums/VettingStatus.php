<?php

declare(strict_types=1);

namespace App\Enums;

enum VettingStatus: string
{
    case NEW = 'new';
    case RUNNING = 'running';
    case FAILED = 'failed';
    case PASSED = 'passed';
}
