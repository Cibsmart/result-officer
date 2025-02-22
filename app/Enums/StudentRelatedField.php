<?php

declare(strict_types=1);

namespace App\Enums;

enum StudentRelatedField: string
{
    case PROGRAM = 'program_id';
    case ENTRY_LEVEL = 'entry_level_id';
    case ENTRY_SESSION = 'entry_session_id';
    case LOCAL_GOVERNMENT = 'local_government_id';
}
