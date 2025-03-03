<?php

declare(strict_types=1);

namespace App\ViewModels\Vetting;

use Inertia\DeferProp;
use Spatie\LaravelData\Data;

final class VettingShowPage extends Data
{
    public function __construct(
        public readonly DeferProp $data,
    ) {
    }
}
