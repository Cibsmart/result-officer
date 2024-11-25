<?php

declare(strict_types=1);

namespace App\Data\Vetting;

use App\Models\VettingReport;
use Spatie\LaravelData\Data;

final class VettingReportData extends Data
{
    public function __construct(
        public int $id,
        public int $vettableId,
        public string $vettableType,
        public string $message,
    ) {
    }

    public static function fromModel(VettingReport $report): self
    {
        return new self(
            id: $report->id,
            vettableId: $report->vettable_id,
            vettableType: $report->vettable_type,
            message: $report->message,
        );
    }
}
