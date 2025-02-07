<?php

declare(strict_types=1);

namespace App\Data\Imports;

use App\Enums\ImportEventStatus;
use App\Enums\StatusColor;
use App\Models\ExcelImportEvent;
use Spatie\LaravelData\Data;

final class ExcelImportEventData extends Data
{
    public function __construct(
        public readonly int $id,
        public readonly string $fileName,
        public readonly ImportEventStatus $status,
        public readonly StatusColor $statusColor,
    ) {
    }

    public static function fromModel(ExcelImportEvent $event): self
    {
        $status = $event->status;
        assert($status instanceof ImportEventStatus);

        return new self(id: $event->id, fileName: $event->file_name, status: $status, statusColor: $status->color());
    }
}
