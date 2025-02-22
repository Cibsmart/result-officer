<?php

declare(strict_types=1);

namespace App\Enums;

enum StudentField: string
{
    case PHONE_NUMBER = 'phone_number';
    case EMAIL = 'email';
    case JAMB_REGISTRATION_NUMBER = 'jamb_registration_number';
}
