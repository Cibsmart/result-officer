<?php

declare(strict_types=1);

namespace App\Enums;

enum CourseStatus: string
{
    case FRESH = 'F';
    case REPEAT = 'R';
}
