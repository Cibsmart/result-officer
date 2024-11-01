<?php

declare(strict_types=1);

namespace App\Enums;

enum CourseType: string
{
    case CORE = 'C';
    case ELECTIVE = 'E';
    case GENERAL = 'G';
    case ANCILLARY = 'A';
}
