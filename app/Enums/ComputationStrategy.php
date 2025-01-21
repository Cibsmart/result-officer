<?php

declare(strict_types=1);

namespace App\Enums;

enum ComputationStrategy: string
{
    case SEMESTER = 'semester';
    case UNIVERSAL = 'universal';
}
