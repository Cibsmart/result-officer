<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\ImportEventStatus;
use App\Enums\ImportEventType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

final class ImportEvent extends Model
{
    /** @param array<string, string> $data */
    public static function new(
        User $user,
        ImportEventType $type,
        array $data,
    ): self {
        $event = new self();
        $event->user_id = $user->id;
        $event->type = $type->value;
        $event->data = $data;
        $event->status = ImportEventStatus::NEW->value;
        $event->save();

        return $event;
    }

    /** @return \Illuminate\Database\Eloquent\Relations\BelongsTo<\App\Models\User, \App\Models\ImportEvent> */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /** @return \Illuminate\Database\Eloquent\Relations\HasMany<\App\Models\RawCourse> */
    public function courses(): HasMany
    {
        return $this->hasMany(RawCourse::class);
    }

    /** @return \Illuminate\Database\Eloquent\Relations\HasMany<\App\Models\RawDepartment> */
    public function departments(): HasMany
    {
        return $this->hasMany(RawDepartment::class);
    }

    public function updateStatus(ImportEventStatus $status): void
    {
        if ($this->status === ImportEventStatus::CANCELLED) {
            return;
        }

        if ($status === ImportEventStatus::COMPLETED) {
            $counts = $this->getCounts();
            $this->processed_count = $counts->processed_count;
            $this->failed_count = $counts->failed_count;
        }

        $this->status = $status->value;
        $this->save();
    }

    public function getCounts(): object
    {
        return $this->courses()->toBase()
            ->selectRaw("count(case when status = 'processed' then 1 end) as processed_count")
            ->selectRaw("count(case when status = 'failed' then 1 end) as failed_count")
            ->firstOrFail();
    }

    /** @return array<string, string> */
    protected function casts(): array
    {
        return [
            'data' => 'json',
            'status' => ImportEventStatus::class,
            'type' => ImportEventType::class,
        ];
    }
}
