<?php

declare(strict_types=1);

namespace App\Enums;

use App\Services\Api\CourseService;
use App\Services\Api\DepartmentService;
use App\Services\Api\RegistrationService;
use App\Services\Api\ResultService;
use App\Services\Api\StudentService;

enum ImportEventType: string
{
    case RESULTS = 'results';
    case COURSES = 'courses';
    case DEPARTMENTS = 'departments';
    case STUDENTS = 'students';
    case REGISTRATIONS = 'registrations';

    /** @return class-string */
    public function service(): string
    {
        return match ($this) {
            self::COURSES => CourseService::class,
            self::DEPARTMENTS => DepartmentService::class,
            self::STUDENTS => StudentService::class,
            self::REGISTRATIONS => RegistrationService::class,
            self::RESULTS => ResultService::class,
        };
    }
}
