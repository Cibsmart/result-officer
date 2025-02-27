<?php

declare(strict_types=1);

namespace App\Data\Vetting;

use App\Enums\StatusColor;
use App\Enums\VettingEventStatus;
use App\Models\VettingEventGroup;
use Spatie\LaravelData\Data;

final class VettingEventGroupData extends Data
{
    public function __construct(
        public readonly int $id,
        public readonly string $slug,
        public readonly string $title,
        public readonly VettingEventStatus $status,
        public readonly StatusColor $statusColor,
        public readonly string $department,
        public readonly string $message,
    ) {
    }

    public static function fromModel(VettingEventGroup $vettingEventGroup): self
    {
        $message = $vettingEventGroup->message;

        $status = $vettingEventGroup->status;

        return new self(
            id: $vettingEventGroup->id,
            slug: $vettingEventGroup->slug,
            title: $vettingEventGroup->title,
            status: $status,
            statusColor: $status->color(),
            department: $vettingEventGroup->department->name,
            message: $message ? $message : '',
        );
    }
}
