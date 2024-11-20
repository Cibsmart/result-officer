<?php

declare(strict_types=1);

namespace App\Enums;

enum StatusColor: string
{
    case GRAY = 'gray';
    case RED = 'red';
    case YELLOW = 'yellow';
    case GREEN = 'green';
    case BLUE = 'blue';
    case PURPLE = 'purple';
    case INDIGO = 'indigo';
    case PINK = 'pink';
}
