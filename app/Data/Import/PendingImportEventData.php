<?php

declare(strict_types=1);

namespace App\Data\Import;

use App\Enums\ImportEventStatus;
use App\Enums\ImportEventType;
use App\Models\ImportEvent;
use App\Models\User;
use Spatie\LaravelData\Data;

final class PendingImportEventData extends Data
{
    public function __construct(
        public readonly string $content,
        public readonly string $date,
        public readonly float $width,
        /** @var array<int, string> $elements */
        public readonly array $elements,
    ) {
    }

    public static function fromModel(ImportEvent $event): self
    {
        /** @var array<string, string> $data */
        $data = $event->data;

        $content = collect($data)
            ->map(fn (string $value, string $key) => strtoupper("$value $key"))
            ->join(', ');

        return new self(
            content: "You initiated download for {$content}",
            date: $event->created_at ? $event->created_at->format('M d, Y') : '',
            width: $event->status->width(),
            elements: collect(ImportEventStatus::cases())->except([0, 3, 6])->all(),
        );
    }

    public static function new(User $user): ?self
    {
        $event = $user->imports()
            ->with('user')
            ->where('type', ImportEventType::COURSES->value)
            ->whereNotIn('status', [ImportEventStatus::FAILED, ImportEventStatus::COMPLETED])
            ->latest()
            ->first();

        return $event
            ? self::fromModel($event)
            : $event;
    }
}
