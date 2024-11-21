<?php

declare(strict_types=1);

namespace App\Data;

use App\Enums\VettingStatus;
use App\Models\VettingStep;
use Spatie\LaravelData\Data;

final class VettingReportData extends Data
{
    public function __construct(
        public string $title,
        public string $description,
        public VettingStatus $status,
    ) {
    }

    public static function fromModel(VettingStep $vettingStep): self
    {
        return new self(title: $vettingStep->type, description: $vettingStep->message, status: $vettingStep->status);
    }
}
