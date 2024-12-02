<?php

declare(strict_types=1);

namespace App\Enums;

enum CumulativeComputationStrategy: string
{
    case SEMESTER = 'semester';
    case UNIVERSAL = 'universal';
}
