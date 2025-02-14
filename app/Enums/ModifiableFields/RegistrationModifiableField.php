<?php

declare(strict_types=1);

namespace App\Enums\ModifiableFields;

enum RegistrationModifiableField: string
{
    case COURSE = 'course';
    case CREDIT_UNIT = 'credit_unit';
}
