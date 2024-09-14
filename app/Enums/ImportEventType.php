<?php

declare(strict_types=1);

namespace App\Enums;

enum ImportEventType: string
{
    case RESULTS = 'results';
    case COURSES = 'courses';
    case DEPARTMENTS = 'departments';
    case STUDENTS = 'students';
    case REGISTRATIONS = 'registrations';
}
