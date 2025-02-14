<?php

declare(strict_types=1);

namespace App\Enums;

enum RecordActionType: string
{
    case CREATE = 'create';
    case UPDATE = 'update';
    case DELETE = 'delete';
}
