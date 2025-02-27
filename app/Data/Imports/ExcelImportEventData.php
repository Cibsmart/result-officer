<?php

declare(strict_types=1);

namespace App\Data\Imports;

use App\Enums\ExcelImportType;
use App\Enums\ImportEventStatus;
use App\Enums\StatusColor;
use App\Models\ExcelImportEvent;
use Spatie\LaravelData\Data;

final class ExcelImportEventData extends Data
{
    public function __construct(
        public readonly int $id,
        public readonly int $userId,
        public readonly string $fileName,
        public readonly ExcelImportType $type,
        public readonly ImportEventStatus $status,
        public readonly StatusColor $statusColor,
        public readonly ?string $message,
        public readonly string $date,
    ) {
    }

    public static function fromModel(ExcelImportEvent $event): self
    {
        $status = $event->status;
        assert($status instanceof ImportEventStatus);

        return new self(
            id: $event->id,
            userId: $event->user_id,
            fileName: $event->file_name,
            type: $event->type,
            status: $status,
            statusColor: $status->color(),
            message: $event->message,
            date: $event->created_at
                ? $event->created_at->format('M d, Y')
                : '',
        );
    }
}
