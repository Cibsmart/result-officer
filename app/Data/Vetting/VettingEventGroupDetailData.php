<?php

declare(strict_types=1);

namespace App\Data\Vetting;

use Illuminate\Support\Collection;
use Spatie\LaravelData\Data;

final class VettingEventGroupDetailData extends Data
{
    public function __construct(
        public readonly VettingEventGroupData $event,
        public readonly Collection $vettings,

    ) {
    }
}
