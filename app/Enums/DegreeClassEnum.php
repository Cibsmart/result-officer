<?php

declare(strict_types=1);

namespace App\Enums;

enum DegreeClassEnum: string
{
    case FIRST_CLASS = 'FIRST CLASS HONOURS';
    case SECOND_CLASS_UPPER = 'SECOND CLASS HONOURS (UPPER DIVISION)';
    case SECOND_CLASS_LOWER = 'SECOND CLASS HONOURS (LOWER DIVISION)';
    case THIRD_CLASS = 'THIRD CLASS HONOURS';
    case PASS = 'PASS';
    case FAIL = 'FAIL';
}
