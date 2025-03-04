<?php

declare(strict_types=1);

namespace App\Data\Vetting;

use App\Models\VettingEventGroup;
use Illuminate\Support\Collection;
use Spatie\LaravelData\Data;

final class VettingEventGroupDetailData extends Data
{
    /** @param \Illuminate\Support\Collection<int, \App\Data\Vetting\VettingEventCurriculumData> $groups */
    public function __construct(
        public readonly Collection $groups,

    ) {
    }

    public static function for(VettingEventGroup $vettingEvent): self
    {
        $groups = [];

        $vettings = $vettingEvent->vettingEvents()
            ->with('student.program.department')
            ->orderBy('id')
            ->get();

        foreach ($vettings->groupBy('program_curriculum_id') as $curriculumId => $vettingList) {
            $groups[] = VettingEventCurriculumData::fromGroupData($curriculumId, VettingData::collect($vettingList));
        }

        return new self(
            groups: VettingEventCurriculumData::collect(collect($groups)),
        );
    }
}
