<?php

declare(strict_types=1);

namespace App\Data\Import;

use App\Enums\ImportEventStatus;
use App\Enums\ImportEventType;
use App\Models\ImportEvent;
use App\Models\User;
use Illuminate\Support\Collection;
use Spatie\LaravelData\Data;

final class ImportEventData extends Data
{
    public function __construct(
        public readonly string $target,
        public readonly ImportEventType $type,
        public readonly string $content,
        public readonly string $statistics,
        public readonly ImportEventStatus $status,
        public readonly string $date,
        public readonly bool $completed,
    ) {
    }

    public static function fromModel(ImportEvent $event): self
    {
        /** @var array<string, string> $data */
        $data = $event->data;

        $content = collect($data)
            ->map(fn (string $value, string $key) => strtoupper("$value $key"))
            ->join(', ');

        $statistics = "(Statistics: Downloaded: {$event->download_count}, Processed: {$event->processed_count}, ";
        $statistics .= "Failed: {$event->failed_count})";

        return new self(
            target: $event->user->name,
            type: $event->type,
            content: "{$content}, downloaded by",
            statistics: $statistics,
            status: $event->status,
            date: $event->created_at ? $event->created_at->diffForHumans() : '',
            completed: $event->status === ImportEventStatus::COMPLETED,
        );
    }

    /** @return \Illuminate\Support\Collection<int, \App\Data\Import\ImportEventData> */
    public static function new(User $user): Collection
    {
        return self::collect($user->imports()
            ->with('user')
            ->where('type', ImportEventType::COURSES->value)
            ->whereIn('status',
                [ImportEventStatus::FAILED, ImportEventStatus::COMPLETED])
            ->latest()
            ->limit(10)
            ->get(),
        );
    }
}
