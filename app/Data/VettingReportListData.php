<?php

declare(strict_types=1);

namespace App\Data;

use App\Models\Student;
use Illuminate\Support\Collection;
use Spatie\LaravelData\Data;

final class VettingReportListData extends Data
{
    public function __construct(
        /** @var \Illuminate\Support\Collection<int, \App\Data\VettingReportData> */
        public Collection $reports,
    ) {
    }

    public static function fromModel(Student $student): self
    {
        $vettingEvent = $student->vettingEvent()->with('vettingSteps')->firstOrFail();

        return new self(reports: VettingReportData::collect($vettingEvent->vettingSteps));
    }
}
