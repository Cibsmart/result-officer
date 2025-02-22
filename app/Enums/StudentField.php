<?php

declare(strict_types=1);

namespace App\Enums;

enum StudentField: string
{
    case GENDER = 'gender';
    case PROGRAM = 'program_id';
    case ENTRY_MODE = 'entry_mode';
    case ENTRY_LEVEL = 'entry_level_id';
    case DATE_OF_BIRTH = 'date_of_birth';
    case LOCAL_GOVERNMENT = 'local_government_id';
    case PHONE_NUMBER = 'phone_number';
    case EMAIL = 'email';
    case JAMB_REGISTRATION_NUMBER = 'jamb_registration_number';
}
