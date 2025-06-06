<?php

declare(strict_types=1);

namespace App\Enums;

enum ChecklistType: string
{
    case REGISTRATION_NUMBER = 'registration_number';
    case IN_COURSE = 'in_course';
    case IN_COURSE_2 = 'in_course_2';
    case EXAM = 'exam';
    case TOTAL = 'total';
    case GRADE = 'grade';
    case CREDIT_UNIT = 'credit_unit';
    case SEMESTER = 'semester';
    case SESSION = 'session';
    case COURSE_CODE = 'course_code';
    case COURSE_TITLE = 'course_title';
    case YEAR = 'year';
    case MONTH = 'month';
    case EXAM_OFFICER = 'exam_officer';

    case CURRICULUM = 'curriculum';
    case ENTRY_MODE = 'entry_mode';
    case ENTRY_SESSION = 'entry_session';
    case LEVEL = 'level';
    case COURSE_TYPE = 'course_type';
}
