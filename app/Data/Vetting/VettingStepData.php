<?php

declare(strict_types=1);

namespace App\Data\Vetting;

use App\Enums\StatusColor;
use App\Enums\VettingStatus;
use App\Models\VettingStep;
use Spatie\LaravelData\Data;

final class VettingStepData extends Data
{
    public function __construct(
        public string $title,
        public string $description,
        public VettingStatus $status,
        public StatusColor $color,
    ) {
    }

    public static function fromModel(VettingStep $vettingStep): self
    {
        $status = $vettingStep->status;

        return new self(
            title: $vettingStep->type,
            description: $vettingStep->message,
            status: $status,
            color: $status->color(),
        );
    }
}
