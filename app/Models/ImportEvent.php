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
    /** @param array $data <string, string> */
    public static function new(User $user, array $data): self
    {
        $event = new self();
        $event->user_id = $user->id;
        $event->type = ImportEventType::COURSES->value;
        $event->data = $data;
        $event->count = 0;
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

    public function updateStatus(ImportEventStatus $status): void
    {
        $this->status = $status->value;
        $this->save();
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
