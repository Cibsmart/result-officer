<?php

declare(strict_types=1);

namespace App\Enums;

enum ScoreType: string
{
    case COURSE_WORK = 'course_work';
    case EXAM = 'exam';
}
