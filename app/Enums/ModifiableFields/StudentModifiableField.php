<?php

declare(strict_types=1);

namespace App\Enums\ModifiableFields;

enum StudentModifiableField: string
{
    case NAME = 'name';
    case GENDER = 'gender';
    case STATUS = 'status';
    case PROGRAM = 'program';
    case ENTRY_MODE = 'entry_mode';
    case ENTRY_LEVEL = 'entry_level';
    case DATE_OF_BIRTH = 'date_of_birth';
    case LOCAL_GOVERNMENT = 'local_government';
    case REGISTRATION_NUMBER = 'registration_number';
}
