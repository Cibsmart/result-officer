<?php

declare(strict_types=1);

namespace App\Enums;

enum CourseType: string
{
    case COMPULSORY = 'C';
    case ELECTIVE = 'E';
    case GENERAL = 'G';
    case REQUIRED = 'R';
}
