<?php

declare(strict_types=1);

namespace App\Enums;

enum ImportEventMethod: string
{
    case ALL = 'all';
    case REGISTRATION_NUMBER = 'registration_number';
    case REGISTRATION_NUMBER_SESSION_SEMESTER = 'registration_number_session_semester';
    case DEPARTMENT_SESSION = 'department_session';
    case DEPARTMENT_SESSION_LEVEL = 'department_session_level';
    case DEPARTMENT_SESSION_SEMESTER = 'department_session_semester';
    case SESSION = 'session';
    case SESSION_COURSE = 'session_course';
}
