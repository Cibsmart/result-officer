<?php

declare(strict_types=1);

namespace App\Data\Vetting;

use App\Models\VettingEventGroup;
use Illuminate\Support\Collection;
use Spatie\LaravelData\Data;

final class VettingEventGroupDetailData extends Data
{
    /** @param \Illuminate\Support\Collection<int, \App\Data\Vetting\VettingData> $vettings */
    public function __construct(
        public readonly VettingEventGroupData $event,
        public readonly Collection $vettings,

    ) {
    }

    public static function for(VettingEventGroup $vettingEvent): self
    {
        $vettings = $vettingEvent->vettingEvents()
            ->with('student.program.department')
            ->get();

        return new self(
            event: VettingEventGroupData::fromModel($vettingEvent),
            vettings: VettingData::collect($vettings),
        );
    }
}
