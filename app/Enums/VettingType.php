<?php

declare(strict_types=1);

namespace App\Enums;

enum VettingType: string
{
    case ORGANIZE_STUDY_YEAR = 'organize_year';
    case MATCH_COURSES = 'match_courses';
    case VALIDATE_RESULTS = 'validate_result';
    case CHECK_CREDIT_UNITS = 'credit_units';
    case CHECK_SEMESTER_CREDIT_UNITS = 'semester_credit';
    case CHECK_FIRST_YEAR_COURSES = 'first_year';
    case CHECK_STUDENT_COURSES = 'student_courses';
    case CHECK_ELECTIVE_COURSES = 'elective_courses';
    case CHECK_FAILED_COURSES = 'failed_courses';
    case CHECK_OTHER_COURSES = 'other_courses';
}
