<?php

declare(strict_types=1);

namespace App\Enums;

enum VettingType: string
{
    case ORGANIZE_STUDY_YEAR = 'organize_year';
    case VALIDATE_RESULTS = 'validate_result';
    case CHECK_SEMESTER_CREDIT_UNITS = 'semester_credit';
    case CHECK_FAILED_COURSES = 'failed_courses';

    case MATCH_COURSES = 'match_courses';
    case CHECK_CREDIT_UNITS = 'credit_units';
    case CHECK_FIRST_YEAR_COURSES = 'first_year';
    case CHECK_CORE_COURSES = 'core_courses';
    case CHECK_ELECTIVE_COURSES = 'elective_courses';
    case CHECK_FAILED_COURSES = 'failed_courses';

    public function passedMessage(): string
    {
        return match ($this) {
            self::ORGANIZE_STUDY_YEAR => 'Study years are organized.',
            self::VALIDATE_RESULTS => 'Results are valid',
            self::CHECK_SEMESTER_CREDIT_LOADS => 'Semester credit loads are within limits',
            self::MATCH_COURSES => 'Courses matches a course in the curriculum',
            self::CHECK_FIRST_YEAR_COURSES => 'Registered all first year courses in the first year of study',
            self::CHECK_CREDIT_UNITS => 'Course credit units match curriculum course units',
            self::CHECK_CORE_COURSES => 'Attempted all Core, Required and General Courses',
            self::CHECK_ELECTIVE_COURSES => 'Attempted necessary elective courses',
            self::CHECK_FAILED_COURSES => 'Passed all attempted courses',
        };
    }
}
