<?php

declare(strict_types=1);

namespace App\Enums\ModifiableFields;

enum StudentModifiableField: string
{
    case EXAM = 'exam';
    case NAME = 'name';
    case COURSE = 'course';
    case GENDER = 'gender';
    case STATUS = 'status';
    case PROGRAM = 'program';
    case IN_COURSE = 'in_course';
    case ENTRY_MODE = 'entry_mode';
    case CREDIT_UNIT = 'credit_unit';
    case ENTRY_LEVEL = 'entry_level';
    case DATE_OF_BIRTH = 'date_of_birth';
    case LOCAL_GOVERNMENT = 'local_government';
    case REGISTRATION_NUMBER = 'registration_number';
}
