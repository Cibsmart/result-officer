<?php

declare(strict_types=1);

namespace App\Data\Vetting;

use App\Models\Student;
use Illuminate\Support\Collection;
use Spatie\LaravelData\Data;

final class VettingStepListData extends Data
{
    public function __construct(
        /** @var \Illuminate\Support\Collection<int, \App\Data\Vetting\VettingStepData> */
        public Collection $items,
    ) {
    }

    public static function fromModel(Student $student): self
    {
        $vettingEvent = $student->vettingEvent()->with('vettingSteps')->firstOrFail();

        return new self(items: VettingStepData::collect($vettingEvent->vettingSteps));
    }
}
