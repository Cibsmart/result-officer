<?php

declare(strict_types=1);

namespace App\Data\Vetting;

use App\Enums\StatusColor;
use App\Enums\VettingStatus;
use App\Enums\VettingType;
use App\Models\VettingStep;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Spatie\LaravelData\Data;

final class VettingStepData extends Data
{
    public function __construct(
        public int $id,
        public VettingType $type,
        public string $title,
        public string $description,
        public VettingStatus $status,
        public StatusColor $color,
        /** @var \Illuminate\Support\Collection<int, \App\Data\Vetting\VettingReportData> */
        public Collection $reports,
    ) {
    }

    public static function fromModel(VettingStep $vettingStep): self
    {
        $status = $vettingStep->status;

        $type = $vettingStep->type;

        return new self(
            id: $vettingStep->id,
            type: $type,
            title: Str::of($type->name)->replace('_', ' ')->value(),
            description: $type->description($status),
            status: $status,
            color: $status->color(),
            reports: VettingReportData::collect($vettingStep->vettingReports),
        );
    }
}
