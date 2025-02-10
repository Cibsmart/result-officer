<?php

declare(strict_types=1);

namespace App\Enums;

enum ChecklistType: string
{
    case REGISTRATION_NUMBER = 'registration_number';
    case IN_COURSE = 'in_course';
    case EXAM = 'exam';
    case TOTAL = 'total';
    case GRADE = 'grade';
    case CREDIT_UNIT = 'credit_unit';
    case SEMESTER = 'semester';
    case SESSION = 'session';
    case COURSE_CODE = 'course_code';
    case YEAR = 'year';
    case MONTH = 'month';

    case CURRICULUM = 'curriculum';
    case ENTRY_MODE = 'entry_mode';
    case ENTRY_SESSION = 'entry_session';
    case LEVEL = 'level';
    case COURSE_TYPE = 'course_type';
}
