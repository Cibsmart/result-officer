<?php

declare(strict_types=1);

namespace App\Enums;

enum ScoreType: string
{
    case IN_COURSE_ASSESSMENT1 = 'in_course_1';
    case IN_COURSE_ASSESSMENT2 = 'inc_course_2';

    case INCEPTION_QUIZ = 'quiz';
    case MID_SEMESTER_EXAM = 'mid_semester';

    case EXAM = 'exam';
}
