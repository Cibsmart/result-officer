<?php

declare(strict_types=1);

namespace App\Enums;

enum VettingType: string
{
    case VALIDATE_RESULTS = 'validate_result';
    case CHECK_MINIMUM_CREDIT = 'minimum_credit';
    case CHECK_MAXIMUM_CREDIT = 'maximum_credit';
    case MATCH_CREDIT_UNITS = 'match_credit';
    case CHECK_FIRST_YEAR_COURSES = 'first_year_courses';
    case CHECK_CURRICULUM_COURSES = 'curriculum_courses';
    case CHECK_TAKEN_COURSES = 'taken_courses';
    case CHECK_OTHER_COURSES = 'other_courses';
}
