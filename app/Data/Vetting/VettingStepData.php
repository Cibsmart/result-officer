<?php

declare(strict_types=1);

namespace App\Data\Vetting;

use App\Enums\StatusColor;
use App\Enums\VettingStatus;
use App\Enums\VettingType;
use App\Models\VettingStep;
use Illuminate\Support\Str;
use Spatie\LaravelData\Data;

final class VettingStepData extends Data
{
    public function __construct(
        public VettingType $type,
        public string $title,
        public string $description,
        public VettingStatus $status,
        public StatusColor $color,
    ) {
    }

    public static function fromModel(VettingStep $vettingStep): self
    {
        $status = $vettingStep->status;

        $type = $vettingStep->type;

        $message = $status === VettingStatus::PASSED
            ? $type->passedMessage()
            : $vettingStep->message;

        return new self(
            type: $type,
            title: Str::of($type->name)->replace('_', ' ')->value(),
            description: $message,
            status: $status,
            color: $status->color(),
        );
    }
}
