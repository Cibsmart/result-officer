<?php

declare(strict_types=1);

namespace App\Enums\ModifiableFields;

enum ProgramModifiableField: string
{
    case CODE = 'code';
    case NAME = 'name';
    case DURATION = 'duration';
    case DEPARTMENT = 'department';
    case PROGRAM_TYPE = 'program_type';
}
