<?php

declare(strict_types=1);

namespace App\Contracts;

use App\Models\VettingEvent;

interface VettingService
{
    public function check(VettingEvent $vettingEvent): void;
}
