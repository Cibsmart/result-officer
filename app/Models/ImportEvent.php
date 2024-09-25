<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\ImportEventMethod;
use App\Enums\ImportEventStatus;
use App\Enums\ImportEventType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

final class ImportEvent extends Model
{
    /** @param array<string, int|string> $data */
    public static function new(
        User $user,
        ImportEventType $type,
        ImportEventMethod $method,
        array $data,
    ): self {
        $event = new self();
        $event->user_id = $user->id;
        $event->type = $type;
        $event->method = $method;
        $event->data = $data;
        $event->status = ImportEventStatus::NEW;
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

    /** @return \Illuminate\Database\Eloquent\Relations\HasMany<\App\Models\RawStudent> */
    public function students(): HasMany
    {
        return $this->hasMany(RawStudent::class);
    }

    /** @return \Illuminate\Database\Eloquent\Relations\HasMany<\App\Models\RawRegistration> */
    public function registrations(): HasMany
    {
        return $this->hasMany(RawRegistration::class);
    }

    /** @return \Illuminate\Database\Eloquent\Relations\HasMany<\App\Models\RawResult> */
    public function results(): HasMany
    {
        return $this->hasMany(RawResult::class);
    }

    public function updateStatus(ImportEventStatus $status): void
    {
        if ($this->status === ImportEventStatus::CANCELLED) {
            return;
        }

        if ($status === ImportEventStatus::COMPLETED) {
            $counts = $this->getCounts();

            $this->saved = $counts->saved;
            $this->processed = $counts->processed;
            $this->duplicate = $counts->duplicate;
            $this->failed = $counts->failed;
            $this->pending = $counts->pending;
        }

        $this->status = $status;
        $this->save();
    }

    public function updateDownloadCount(int $count): void
    {
        $this->downloaded = $count;
        $this->save();
    }

    /** @return object{saved: int, processed: int, duplicate: int, failed: int, pending: int} */
    public function getCounts(): object
    {
        return $this->{$this->type->value}()->toBase()
            ->selectRaw('count(*) as saved')
            ->selectRaw("count(case when status = 'processed' then 1 end) as processed")
            ->selectRaw("count(case when status = 'duplicate' then 1 end) as duplicate")
            ->selectRaw("count(case when status = 'failed' then 1 end) as failed")
            ->selectRaw("count(case when status = 'pending' then 1 end) as pending")
            ->firstOrFail();
    }

    public function setMessage(string $message): void
    {
        $this->message = $message;
        $this->save();
    }

    /**
     * @return array{data: 'json', status: 'App\Enums\ImportEventStatus',
     *     type: 'App\Enums\ImportEventType', method: 'App\Enums\ImportEventMethod', }
     */
    protected function casts(): array
    {
        return [
            'data' => 'json',
            'method' => ImportEventMethod::class,
            'status' => ImportEventStatus::class,
            'type' => ImportEventType::class,
        ];
    }
}
