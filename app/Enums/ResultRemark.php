<?php

declare(strict_types=1);

namespace App\Enums;

enum ResultRemark: string
{
    case PASSED = 'PAS';
    case FAILED = 'FAL';
    case ABSENT = 'ABS';
    case MALPRACTICE = 'MAL';
}
