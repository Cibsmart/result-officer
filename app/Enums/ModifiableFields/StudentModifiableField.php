<?php

declare(strict_types=1);

namespace App\Enums\ModifiableFields;

enum StudentModifiableField: string
{
    case NAME = 'name';
    case EMAIL = 'email';
//    case COURSE = 'course';
//    case GENDER = 'gender';
    case STATUS = 'status';
//    case PROGRAM = 'program';
//    case ENTRY_MODE = 'entry_mode';
    case RESULT = 'result';
//    case ENTRY_LEVEL = 'entry_level';
    case PHONE_NUMBER = 'phone_number';
//    case DATE_OF_BIRTH = 'date_of_birth';
//    case LOCAL_GOVERNMENT = 'local_government';
    case REGISTRATION_NUMBER = 'registration_number';
    case JAMB_REGISTRATION_NUMBER = 'jamb_registration_number';
}
