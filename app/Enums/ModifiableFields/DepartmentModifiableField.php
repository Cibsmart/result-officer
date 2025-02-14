<?php

declare(strict_types=1);

namespace App\Enums\ModifiableFields;

enum DepartmentModifiableField: string
{
    case NAME = 'name';
    case CODE = 'code';
    case FACULTY = 'faculty';
}
